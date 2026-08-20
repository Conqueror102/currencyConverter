<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Synchronize currency metadata daily at midnight
Schedule::command('rates:sync-currencies')
    ->daily()
    ->withoutOverlapping();

// Synchronize latest rates every 3 hours on weekdays
Schedule::command('rates:sync-latest')
    ->cron('0 */3 * * 1-5')
    ->withoutOverlapping();

// Synchronize crypto P2P & parallel market rates every 30 minutes
Schedule::command('rates:sync-parallel')
    ->everyThirtyMinutes()
    ->withoutOverlapping();
