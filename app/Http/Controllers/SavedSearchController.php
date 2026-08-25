<?php

namespace App\Http\Controllers;

use App\Jobs\DeliverLead;
use App\Models\Lead;
use App\Models\SavedSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SavedSearchController extends Controller
{
    /** Save the current /listings filters as an email alert. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email:rfc',
            'name' => 'nullable|string|max:80',
            'city' => 'nullable|string|max:60',
            'min' => 'nullable|integer|min:0',
            'max' => 'nullable|integer|min:0',
            'beds' => 'nullable|integer|between:1,10',
            'dwelling' => 'nullable|in:detached,attached,multi,multi5',
        ]);

        $criteria = array_filter($request->only(['city', 'min', 'max', 'beds', 'dwelling']),
            fn ($v) => $v !== null && $v !== '');

        $search = SavedSearch::firstOrCreate(
            ['email' => strtolower($data['email']), 'criteria' => $criteria],
            ['name' => $data['name'] ?? null, 'token' => Str::random(40), 'last_notified_at' => now()],
        );

        // A saved search is a high-intent buyer lead — feed the pipeline once.
        if ($search->wasRecentlyCreated) {
            $lead = Lead::create([
                'name' => $data['name'] ?? '',
                'email' => $search->email,
                'phone' => '',
                'interest' => 'buy',
                'message' => 'Saved a listing search with email alerts: '.$search->summary(),
                'is_spam' => false,
                'source_page' => (string) $request->headers->get('referer'),
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
            ]);
            DeliverLead::dispatchAfterResponse($lead);
        }

        return back()->with('alert_saved', $search->summary());
    }

    public function unsubscribe(string $token)
    {
        $search = SavedSearch::where('token', $token)->first();
        $search?->delete();

        return response('<div style="max-width:520px;margin:80px auto;font-family:Archivo,Arial,sans-serif;text-align:center;color:#0F1E2E;">'
            .'<h2>You\'re unsubscribed.</h2><p style="color:#48586B">'
            .($search ? 'That saved search has been deleted — no more alerts.' : 'That alert was already removed.')
            .'</p><p><a href="/listings" style="color:#C8102E;font-weight:700;">Back to the search &rarr;</a></p></div>');
    }
}
