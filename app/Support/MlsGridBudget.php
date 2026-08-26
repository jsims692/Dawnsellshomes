<?php

namespace App\Support;

/**
 * Self-imposed MLS GRID usage budget, calibrated to the limits their
 * 08/25/2026 warning email states outright:
 *
 *   warning:    7,200 req/hr · 3,072 MB/hr · 4 rps · 40,000 req/24h · 40 GB/24h
 *   suspension: 18,000 req/hr · 4,096 MB/hr · 6 rps · 60,000 req/24h · 60 GB/24h
 *
 * Two lessons encoded here: (1) their byte meter matches OUR decompressed
 * count (their 3,276MB warning hour was our 3,206MB ledger hour — the old
 * "wire bytes are ~1/8th" assumption was wrong), and (2) their 24-hour
 * windows are ROLLING, so we track per-hour buckets and sum the last 24
 * rather than trusting a calendar-day counter that resets at midnight.
 *
 * Every API-touching command checks allow() before each request and
 * record()s after. Background transfers (photo backfill) see a reduced
 * budget so they can never starve the hourly sync cycle.
 */
class MlsGridBudget
{
    private const HOUR_REQUESTS = 4000;            // warning limit: 7,200

    private const HOUR_BYTES = 2_800_000_000;      // warning limit: 3,072 MB

    private const ROLL_REQUESTS = 36000;           // rolling 24h; warning: 40,000

    private const ROLL_BYTES = 30_000_000_000;     // rolling 24h; warning: 40 GB

    private const BG_RESERVE_HOUR = 800;

    private const BG_RESERVE_ROLL = 6000;

    public static function allow(bool $background = false): bool
    {
        [$hour, $roll] = self::usage();
        $hourCap = self::HOUR_REQUESTS - ($background ? self::BG_RESERVE_HOUR : 0);
        $rollCap = self::ROLL_REQUESTS - ($background ? self::BG_RESERVE_ROLL : 0);

        return $hour['req'] < $hourCap && $hour['bytes'] < self::HOUR_BYTES
            && $roll['req'] < $rollCap && $roll['bytes'] < self::ROLL_BYTES;
    }

    public static function record(int $bytes): void
    {
        $hours = self::buckets();
        $key = (string) (int) floor(time() / 3600);
        $hours[$key] = [
            'req' => ($hours[$key]['req'] ?? 0) + 1,
            'bytes' => ($hours[$key]['bytes'] ?? 0) + $bytes,
        ];
        file_put_contents(self::file(), json_encode(['hours' => $hours]));
    }

    public static function summary(): string
    {
        [$hour, $roll] = self::usage();

        return sprintf('hour: %d req / %dMB — rolling 24h: %d req / %dMB',
            $hour['req'], (int) ($hour['bytes'] / 1_000_000), $roll['req'], (int) ($roll['bytes'] / 1_000_000));
    }

    /** @return array{0: array{req:int,bytes:int}, 1: array{req:int,bytes:int}} */
    private static function usage(): array
    {
        $hours = self::buckets();
        $nowH = (int) floor(time() / 3600);
        $hour = $hours[(string) $nowH] ?? ['req' => 0, 'bytes' => 0];
        $roll = ['req' => 0, 'bytes' => 0];
        foreach ($hours as $b) {
            $roll['req'] += $b['req'] ?? 0;
            $roll['bytes'] += $b['bytes'] ?? 0;
        }

        return [$hour, $roll];
    }

    /** Per-hour buckets for the trailing 24h (legacy single-window files migrate in). */
    private static function buckets(): array
    {
        $state = json_decode((string) @file_get_contents(self::file()), true) ?: [];
        $nowH = (int) floor(time() / 3600);

        $hours = $state['hours'] ?? null;
        if (! is_array($hours)) {
            // Migrate the old {hour, day} shape: keep the current hour, and
            // park the rest of the calendar day's usage mid-window so the
            // rolling total stays honest while it ages out.
            $hours = [];
            if (isset($state['hour']['ts'])) {
                $hours[(string) $state['hour']['ts']] = [
                    'req' => (int) ($state['hour']['req'] ?? 0),
                    'bytes' => (int) ($state['hour']['bytes'] ?? 0),
                ];
            }
            $carryReq = max(0, (int) ($state['day']['req'] ?? 0) - (int) ($state['hour']['req'] ?? 0));
            $carryBytes = max(0, (int) ($state['day']['bytes'] ?? 0) - (int) ($state['hour']['bytes'] ?? 0));
            if ($carryReq > 0 || $carryBytes > 0) {
                $hours[(string) ($nowH - 12)] = ['req' => $carryReq, 'bytes' => $carryBytes];
            }
        }

        foreach (array_keys($hours) as $ts) {
            if ((int) $ts < $nowH - 24) {
                unset($hours[$ts]);
            }
        }

        return $hours;
    }

    private static function file(): string
    {
        return storage_path('app/mlsgrid-usage.json');
    }
}
