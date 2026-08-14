<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Handles the sitewide contact form, which POSTs to "/" with
     * form-name=contact (the same contract the old Netlify form used, so the
     * existing markup and JS keep working unchanged). Every submission is
     * stored; non-spam leads are forwarded to the CRM webhook when configured.
     */
    public function store(Request $request)
    {
        if ($request->input('form-name') !== 'contact') {
            abort(404);
        }

        $lead = Lead::create([
            'name' => (string) $request->input('name'),
            'email' => (string) $request->input('email'),
            'phone' => (string) $request->input('phone'),
            'interest' => (string) $request->input('interest'),
            'message' => (string) $request->input('message'),
            // honeypot: real visitors never fill bot-field
            'is_spam' => filled($request->input('bot-field')),
            'source_page' => (string) $request->headers->get('referer'),
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        if (! $lead->is_spam) {
            $this->forwardToKvCore($lead);
            $this->forwardToWebhook($lead);
        }

        // The form's JS treats any response as success; non-JS fallback gets a redirect.
        return $request->expectsJson() || $request->ajax()
            ? response()->json(['ok' => true])
            : redirect('/#contact');
    }

    /**
     * Push the lead into kvCORE/BoldTrail as a contact (POST /v2/public/contact).
     * The message goes along as notes; interest maps to kvCORE's deal_type.
     */
    private function forwardToKvCore(Lead $lead): void
    {
        $token = config('services.kvcore.token');
        if (! $token) {
            return;
        }

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
            'note' => null,
        ]);
        $payload['note'] = $payload['notes'] ?? null;
        $payload = array_filter($payload);

        try {
            $response = Http::timeout(8)
                ->withToken($token)
                ->acceptJson()
                ->post('https://api.kvcore.com/v2/public/contact', $payload);

            // A repeat submission with an identical payload returns kvCORE's
            // dedupe message with a non-2xx-shaped body; both count as delivered.
            if ($response->successful() || str_contains($response->body(), 'already received')) {
                $lead->update(['forwarded_at' => now()]);
            } else {
                Log::warning('kvCORE forward rejected', ['lead_id' => $lead->id, 'status' => $response->status(), 'body' => substr($response->body(), 0, 300)]);
            }
        } catch (\Throwable $e) {
            // Lead is safe in the DB either way; forwarding can be retried.
            Log::warning('kvCORE forward failed', ['lead_id' => $lead->id, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Optional secondary delivery to a generic webhook (e.g. a Zapier catch
     * hook), controlled by LEAD_WEBHOOK_URL.
     */
    private function forwardToWebhook(Lead $lead): void
    {
        $webhook = config('services.lead_webhook.url');
        if (! $webhook) {
            return;
        }

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
}
