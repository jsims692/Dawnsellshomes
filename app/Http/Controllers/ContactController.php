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
        $form = $request->input('form-name');
        if (! in_array($form, ['contact', 'property-management'], true)) {
            abort(404);
        }

        // The property-management form carries extra fields; fold them into the
        // message so every downstream consumer (DB, kvCORE, email) sees them.
        $message = (string) $request->input('message');
        if ($form === 'property-management') {
            $extras = array_filter([
                $request->input('property_address') ? 'Property: '.$request->input('property_address') : null,
                $request->input('rental_type') ? 'Rental type: '.$request->input('rental_type') : null,
            ]);
            $message = trim(implode("\n", $extras)."\n\n".$message);
        }

        $lead = Lead::create([
            'name' => (string) $request->input('name'),
            'email' => (string) $request->input('email'),
            'phone' => (string) $request->input('phone'),
            'interest' => $form === 'property-management' ? 'property-management' : (string) $request->input('interest'),
            'message' => $message,
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
