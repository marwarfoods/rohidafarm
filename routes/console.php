<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Shiprocket Checkout failsafe: pick up orders whose webhook was missed (needs the scheduler cron).
\Illuminate\Support\Facades\Schedule::command('shiprocket-checkout:reconcile')->everyTenMinutes()->withoutOverlapping();
