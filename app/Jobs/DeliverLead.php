<?php

namespace App\Jobs;

use App\Mail\LeadNotification;
use App\Models\Lead;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Delivers a stored lead to kvCORE, the optional webhook, and the team's
 * email. Dispatched after the HTTP response is sent, so slow or unreachable
 * third parties can never delay the visitor's form submission.
 */
class DeliverLead
{
    use Dispatchable;

    public function __construct(public Lead $lead)
    {
    }

    public function handle(): void
    {
        $this->forwardToKvCore();
        $this->forwardToWebhook();
        $this->notifyByEmail();
    }

    private function forwardToKvCore(): void
    {
        $token = config('services.kvcore.token');
        if (! $token) {
            return;
        }

        $lead = $this->lead;
        $name = trim((string) $lead->name);
        $lastSpace = strrpos($name, ' ');

        $payload = array_filter([
            'first_name' => $lastSpace ? substr($name, 0, $lastSpace) : $name,
            'last_name' => $lastSpace ? substr($name, $lastSpace + 1) : null,
            'email' => $lead->email ?: null,
            'cell_phone_1' => preg_replace('/\D+/', '', (string) $lead->phone) ?: null,
            'deal_type' => match ($lead->interest) {
                'buy', 'invest', 'both' => 'buyer',
                'sell', 'value' => 'seller',
                'rent' => 'renter',
                default => null,
            },
            'source' => 'dawnsellshomes.com website',
            'notes' => trim(($lead->interest ? "Interest: {$lead->interest}\n" : '')
                .($lead->message ? "Message: {$lead->message}\n" : '')
                .($lead->source_page ? "Submitted from: {$lead->source_page}" : '')) ?: null,
        ]);
        $payload['note'] = $payload['notes'] ?? null;
        $payload = array_filter($payload);

        try {
            $response = Http::timeout(8)
                ->withToken($token)
                ->acceptJson()
                ->post('https://api.kvcore.com/v2/public/contact', $payload);

            // Delivered outcomes: created (2xx), identical payload deduped, or
            // the contact already exists in kvCORE (409).
            if ($response->successful()
                || $response->status() === 409
                || str_contains($response->body(), 'already received')) {
                $this->lead->update(['forwarded_at' => now()]);
            } else {
                Log::warning('kvCORE forward rejected', ['lead_id' => $lead->id, 'status' => $response->status(), 'body' => substr($response->body(), 0, 300)]);
            }
        } catch (\Throwable $e) {
            // Lead is safe in the DB either way; forwarding can be retried.
            Log::warning('kvCORE forward failed', ['lead_id' => $lead->id, 'error' => $e->getMessage()]);
        }
    }

    private function forwardToWebhook(): void
    {
        $webhook = config('services.lead_webhook.url');
        if (! $webhook) {
            return;
        }

        $lead = $this->lead;

        try {
            Http::timeout(5)->post($webhook, [
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'interest' => $lead->interest,
                'message' => $lead->message,
                'source_page' => $lead->source_page,
                'submitted_at' => $lead->created_at->toIso8601String(),
            ]);
            $lead->forwarded_at ?? $lead->update(['forwarded_at' => now()]);
        } catch (\Throwable $e) {
            Log::warning('Lead webhook forward failed', ['lead_id' => $lead->id, 'error' => $e->getMessage()]);
        }
    }

    private function notifyByEmail(): void
    {
        $recipients = config('services.lead_notify.recipients', []);
        if (empty($recipients)) {
            return;
        }

        try {
            Mail::to($recipients)->send(new LeadNotification($this->lead->refresh()));
        } catch (\Throwable $e) {
            Log::warning('Lead email notification failed', ['lead_id' => $this->lead->id, 'error' => $e->getMessage()]);
        }
    }
}
