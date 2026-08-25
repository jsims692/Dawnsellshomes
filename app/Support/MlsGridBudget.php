<?php

namespace App\Support;

/**
 * Self-imposed MLS GRID usage budget — deliberately far inside their WARNING
 * thresholds (7,200 req + 3,072MB transfer per hour; 40k req + 40GB per day).
 * Bytes are measured decompressed (what we can see), so the hourly byte
 * budget of 5GB decompressed corresponds to roughly 1–1.5GB on the wire —
 * about one full replication per hour, at most.
 *
 * Suspension on 08/24/2026 (6.7GB transfer in an hour from repeated full
 * crawls) is why this exists. Every API-touching command checks allow()
 * before each request and record()s after.
 */
class MlsGridBudget
{
    private const HOUR_REQUESTS = 4000;

    private const HOUR_BYTES = 5_000_000_000;      // decompressed

    private const DAY_REQUESTS = 30000;

    private const DAY_BYTES = 30_000_000_000;      // decompressed

    public static function allow(): bool
    {
        [$hour, $day] = self::windows();

        return $hour['req'] < self::HOUR_REQUESTS && $hour['bytes'] < self::HOUR_BYTES
            && $day['req'] < self::DAY_REQUESTS && $day['bytes'] < self::DAY_BYTES;
    }

    public static function record(int $bytes): void
    {
        [$hour, $day] = self::windows();
        $hour['req']++;
        $hour['bytes'] += $bytes;
        $day['req']++;
        $day['bytes'] += $bytes;
        file_put_contents(self::file(), json_encode(['hour' => $hour, 'day' => $day]));
    }

    public static function summary(): string
    {
        [$hour, $day] = self::windows();

        return sprintf('hour: %d req / %dMB — day: %d req / %dMB',
            $hour['req'], (int) ($hour['bytes'] / 1048576), $day['req'], (int) ($day['bytes'] / 1048576));
    }

    /** @return array{0: array{ts:int,req:int,bytes:int}, 1: array{ts:int,req:int,bytes:int}} */
    private static function windows(): array
    {
        $state = json_decode((string) @file_get_contents(self::file()), true) ?: [];
        $hourTs = (int) floor(time() / 3600);
        $dayTs = (int) floor(time() / 86400);
        $hour = ($state['hour']['ts'] ?? null) === $hourTs ? $state['hour'] : ['ts' => $hourTs, 'req' => 0, 'bytes' => 0];
        $day = ($state['day']['ts'] ?? null) === $dayTs ? $state['day'] : ['ts' => $dayTs, 'req' => 0, 'bytes' => 0];

        return [$hour, $day];
    }

    private static function file(): string
    {
        return storage_path('app/mlsgrid-usage.json');
    }
}
