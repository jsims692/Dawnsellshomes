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

        if (! $lead->is_spam && ($webhook = config('services.lead_webhook.url'))) {
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
                $lead->update(['forwarded_at' => now()]);
            } catch (\Throwable $e) {
                // Lead is safe in the DB either way; forwarding can be retried.
                Log::warning('Lead webhook forward failed', ['lead_id' => $lead->id, 'error' => $e->getMessage()]);
            }
        }

        // The form's JS treats any response as success; non-JS fallback gets a redirect.
        return $request->expectsJson() || $request->ajax()
            ? response()->json(['ok' => true])
            : redirect('/#contact');
    }
}
