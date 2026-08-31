<?php

namespace App\Support;

use App\Models\Lead;
use Illuminate\Http\Request;

/**
 * Contact-form spam scoring. The honeypot alone (Aug 2026) let through a
 * wave of bots that skip hidden fields entirely — a form-testing bot cycling
 * through machine-translated "what's your price" one-liners across fake
 * emails/IPs, plus a run of self-pitching "we offer virtual assistants / SEO
 * / bookkeeping" cold-outreach bots. None of these are a real buyer or
 * seller, so each signal below targets exactly how they differ from one.
 */
class LeadSpam
{
    /** A real visitor takes at least this long to read the form and type into it. */
    private const MIN_FORM_SECONDS = 3;

    /** A prior submission from the same address this recently means this one is a repeat/bot, not a second inquiry. */
    private const REPEAT_WINDOW_DAYS = 30;

    /**
     * Lifted near-verbatim from the actual cold-pitch bots that hit this
     * form (Aug 2026) — self-referential "we offer a service" language no
     * genuine buyer or seller uses about their own home search.
     */
    private const PITCH_PHRASES = [
        'virtual assistant', 'reaching out because we offer', 'seo strategy',
        'seo service', 'search engine ranker', 'increase your ranking',
        'video to explain what you do', 'bookkeeping services', 'digital marketing',
        'grow your business', 'custom-built ai', 'our pricing starts',
        'prices start from', 'link building', 'guest post',
    ];

    public static function check(Request $request, string $email, string $message): bool
    {
        if (filled($request->input('bot-field'))) {
            return true;
        }

        $ts = (float) $request->input('form_ts');
        if ($ts <= 0) {
            return true; // never ran the page's JS — a direct replay of the endpoint
        }
        $elapsedMs = microtime(true) * 1000 - $ts;
        // Only penalize a small NON-NEGATIVE elapsed time — a negative value
        // means the visitor's clock runs ahead of the server's, which is
        // clock skew, not evidence of a bot, and must not count against them.
        if ($elapsedMs >= 0 && $elapsedMs < self::MIN_FORM_SECONDS * 1000) {
            return true;
        }

        $email = mb_strtolower(trim($email));
        if ($email !== '' && Lead::whereRaw('LOWER(email) = ?', [$email])
            ->where('created_at', '>=', now()->subDays(self::REPEAT_WINDOW_DAYS))->exists()) {
            return true;
        }

        $haystack = mb_strtolower($message);
        foreach (self::PITCH_PHRASES as $phrase) {
            if (str_contains($haystack, $phrase)) {
                return true;
            }
        }

        return false;
    }
}
