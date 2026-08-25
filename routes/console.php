<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


use Illuminate\Support\Facades\Schedule;

// MLS GRID replication: hourly keeps us far inside the 12-hour IDX refresh rule.
// No-ops harmlessly until MLSGRID_TOKEN is set.
Schedule::command('mls:sync')->hourly()->withoutOverlapping();

// Cache primary photos for listings the sync just added/changed (signed MLS
// GRID media URLs expire and rate-limit hotlinks, so pages use local copies).
Schedule::command('mls:geocode')->hourlyAt(12)->withoutOverlapping();
Schedule::command('mls:media', ['--limit' => 500])->hourlyAt(20)->withoutOverlapping();
