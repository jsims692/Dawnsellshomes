<?php

namespace App\Http\Controllers;

use App\Jobs\DeliverLead;
use App\Models\Lead;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Handles the sitewide contact form, which POSTs to "/" with
     * form-name=contact (the same contract the old Netlify form used, so the
     * existing markup and JS keep working unchanged). Every submission is
     * stored; delivery to kvCORE + webhook + email runs after the response.
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
            DeliverLead::dispatchAfterResponse($lead);
        }

        // The form's JS treats any response as success; non-JS fallback gets a redirect.
        return $request->expectsJson() || $request->ajax()
            ? response()->json(['ok' => true])
            : redirect('/#contact');
    }
}
