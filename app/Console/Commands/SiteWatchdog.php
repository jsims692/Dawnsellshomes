<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

/**
 * Self-monitoring: every 10 minutes, fetch the site's own key pages and
 * scan the log for fresh errors. Two consecutive bad checks (down or
 * slower than 8s) or new application errors trigger an email to the
 * team — with a 6-hour cooldown per alert type so an incident sends one
 * email, not seventy. The /listings glob regression sat for days because
 * only a human noticed; this exists so a robot notices first.
 */
class SiteWatchdog extends Command
{
    protected $signature = 'site:watchdog';

    protected $description = 'Check site health + fresh log errors; email on trouble';

    private const PAGES = ['https://dawnsellshomes.com/', 'https://dawnsellshomes.com/listings'];

    private const SLOW_SECONDS = 8.0;

    public function handle(): int
    {
        $problems = [];
        foreach (self::PAGES as $url) {
            $t = microtime(true);
            try {
                $resp = Http::timeout(20)->withHeaders(['User-Agent' => 'DawnSellsHomes-Watchdog'])->get($url);
                $secs = microtime(true) - $t;
                if (! $resp->successful()) {
                    $problems[] = "$url returned HTTP ".$resp->status();
                } elseif ($secs > self::SLOW_SECONDS) {
                    $problems[] = sprintf('%s took %.1fs', $url, $secs);
                }
            } catch (\Throwable $e) {
                $problems[] = "$url unreachable: ".substr($e->getMessage(), 0, 120);
            }
        }

        // Two consecutive bad checks before alerting (one blip is a blip).
        if ($problems !== []) {
            if (cache()->get('watchdog-strike') && cache()->add('watchdog-alerted', 1, 21600)) {
                $this->alert_("Site health alert", implode("\n", $problems)
                    ."\n\nSecond consecutive failed check (checks run every 10 minutes).");
            }
            cache()->put('watchdog-strike', 1, 1200);
        } else {
            cache()->forget('watchdog-strike');
        }

        // Fresh application errors since the last scan. The byte-offset
        // bookmark lives in a FILE, not the cache — a cache:clear used to
        // erase it, and the next scan re-alerted on already-seen errors.
        $log = storage_path('logs/laravel.log');
        $posFile = storage_path('app/watchdog.logpos');
        if (is_file($log)) {
            $size = filesize($log);
            $mark = (int) @file_get_contents($posFile);
            if ($size < $mark) {
                $mark = 0; // rotated
            }
            if ($size > $mark) {
                $fh = fopen($log, 'r');
                fseek($fh, $mark);
                $chunk = stream_get_contents($fh, min($size - $mark, 2_000_000));
                fclose($fh);
                @file_put_contents($posFile, (string) $size);
                preg_match_all('/^\[\d{4}-[^\]]+\] production\.ERROR: (.{0,160})/m', $chunk, $m);
                $errors = array_slice(array_unique($m[1] ?? []), 0, 8);
                if ($errors !== [] && cache()->add('watchdog-log-alerted', 1, 21600)) {
                    $this->alert_('New application errors on dawnsellshomes.com',
                        "Fresh errors in the last scan window:\n\n- ".implode("\n- ", $errors)
                        ."\n\n(At most one of these emails per 6 hours; full detail in storage/logs/laravel.log.)");
                }
            }
        }

        return self::SUCCESS;
    }

    private function alert_(string $subject, string $body): void
    {
        try {
            Mail::raw($body."\n\n— site watchdog", function ($m) use ($subject) {
                $m->to(explode(',', (string) env('LEAD_NOTIFY_EMAILS', 'jsims692@gmail.com')))
                    ->subject('⚠️ '.$subject);
            });
        } catch (\Throwable) {
            // mail down while site down — nothing more we can do from inside
        }
    }
}
