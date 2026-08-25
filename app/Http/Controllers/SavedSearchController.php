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
            'city' => 'nullable',
            'city.*' => 'string|max:60',
            'min' => 'nullable|integer|min:0',
            'max' => 'nullable|integer|min:0',
            'beds' => 'nullable|integer|between:1,10',
            'dwelling' => 'nullable|in:detached,attached,multi,multi5',
            'waterfront' => 'nullable|boolean',
            'basement' => 'nullable|boolean',
            'garage' => 'nullable|integer|between:1,5',
        ]);

        $criteria = array_filter($request->only(['city', 'min', 'max', 'beds', 'dwelling', 'waterfront', 'basement', 'garage']),
            fn ($v) => $v !== null && $v !== '' && $v !== []);

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

    /** Every saved search for this subscriber, each with its own remove link. */
    public function manage(string $token)
    {
        $mine = SavedSearch::where('token', $token)->firstOrFail();
        $searches = SavedSearch::where('email', $mine->email)->where('active', true)->get();
        $rows = $searches->map(fn ($s) => '<div style="display:flex;justify-content:space-between;gap:14px;align-items:center;border:1px solid #DEE6EE;border-radius:10px;padding:14px 16px;margin:0 0 10px;">'
            .'<div><strong>'.e($s->summary()).'</strong><br><span style="color:#8A99AA;font-size:12px;">saved '.$s->created_at->format('M j, Y').'</span></div>'
            .'<a href="/alerts/unsubscribe/'.$s->token.'" style="color:#C8102E;font-weight:700;font-size:13px;">Remove</a></div>')->implode('');

        return response('<div style="max-width:560px;margin:60px auto;font-family:Archivo,Arial,sans-serif;color:#0F1E2E;padding:0 20px;">'
            .'<h2 style="margin-bottom:4px;">Your listing alerts</h2>'
            .'<p style="color:#48586B;margin-top:0;">'.e($mine->email).'</p>'.$rows
            .'<p style="margin-top:18px;"><a href="/listings" style="color:#C8102E;font-weight:700;">Save another search &rarr;</a></p></div>');
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
