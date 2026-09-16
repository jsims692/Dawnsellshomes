<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Nightly database backup. Skips the MLS replica tables (listings, rooms,
 * features re-sync from the feed in an hour) so the dump stays small and
 * captures what is genuinely irreplaceable: leads, pages/articles, sales
 * history, saved searches, favorites-adjacent state, telemetry. Gzipped,
 * rotated at 14 days, in storage/app/backups (pulled offsite weekly).
 */
class DbBackup extends Command
{
    protected $signature = 'db:backup';

    protected $description = 'Dump the database (minus MLS replica tables), gzip, rotate 14 days';

    public function handle(): int
    {
        $dir = storage_path('app/backups');
        if (! is_dir($dir)) {
            mkdir($dir, 0770, true);
        }

        $db = config('database.connections.mysql');
        $file = $dir.'/db-'.now()->format('Ymd-His').'.sql.gz';
        $ignores = collect(['listings', 'listing_rooms', 'listing_features'])
            ->map(fn ($t) => '--ignore-table='.escapeshellarg($db['database'].'.'.$t))->implode(' ');

        $cmd = sprintf(
            'MYSQL_PWD=%s mysqldump --single-transaction --quick --no-tablespaces -h %s -u %s %s %s | gzip > %s',
            escapeshellarg($db['password']),
            escapeshellarg($db['host']),
            escapeshellarg($db['username']),
            $ignores,
            escapeshellarg($db['database']),
            escapeshellarg($file),
        );
        exec($cmd, $out, $rc);

        if ($rc !== 0 || ! is_file($file) || filesize($file) < 10_000) {
            @unlink($file);
            $this->error('Backup failed (rc='.$rc.').');

            return self::FAILURE;
        }

        foreach (glob($dir.'/db-*.sql.gz') ?: [] as $old) {
            if (filemtime($old) < now()->subDays(14)->timestamp) {
                @unlink($old);
            }
        }
        $this->info('Backup written: '.basename($file).' ('.round(filesize($file) / 1_048_576, 1).' MB)');

        return self::SUCCESS;
    }
}
