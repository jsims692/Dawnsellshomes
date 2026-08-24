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
