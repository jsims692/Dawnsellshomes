<?php

namespace App\Console\Commands;

use App\Mail\ListingAlert;
use App\Models\SavedSearch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/** Emails saved-search subscribers about listings that appeared since their
 *  last alert. Runs after each sync; sends only when there are matches. */
class MlsAlerts extends Command
{
    protected $signature = 'mls:alerts {--dry : Report without sending}';

    protected $description = 'Send new-listing email alerts for saved searches';

    public function handle(): int
    {
        $sent = 0;
        foreach (SavedSearch::where('active', true)->cursor() as $search) {
            $new = $search->matches()
                ->where('listings.created_at', '>', $search->last_notified_at ?? $search->created_at)
                ->orderByDesc('listings.created_at')->limit(12)->get();
            if ($new->isEmpty()) {
                continue;
            }
            if (! $this->option('dry')) {
                Mail::to($search->email)->send(new ListingAlert($search, $new));
                $search->update(['last_notified_at' => now()]);
            }
            $sent++;
            $this->line(($this->option('dry') ? '[dry] ' : '')."{$search->email}: {$new->count()} new match(es)");
        }
        $this->info("Alerts: {$sent} email(s) ".($this->option('dry') ? 'would be ' : '')

            .'sent.');

        return self::SUCCESS;
    }
}
