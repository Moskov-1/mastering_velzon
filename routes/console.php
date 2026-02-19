<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule::command('app:reset-expired-ads')
//     ->daily()
//     ->appendOutputTo(storage_path('logs/scheduled-tasks.log'));

// Schedule::command('app:delete-expiry')
//     ->everyFifteenMinutes()
//     ->appendOutputTo(storage_path('logs/scheduled-tasks.log'));

// Schedule::command('app:expiry-reminder')
//     ->everySixHours()
//     ->withoutOverlapping(10)
//     ->runInBackground()
//     ->appendOutputTo(storage_path('logs/scheduled-tasks.log'))
//     ->onFailure(function () {
//         Log::error('Expiry reminder command failed');
//     });

// Schedule::command('app:general-reminder')
//     ->everyThirtyMinutes()
//     ->withoutOverlapping(10)
//     ->runInBackground()
//     ->appendOutputTo(storage_path('logs/scheduled-tasks.log'))
//     ->onFailure(function () {
//         Log::error('General reminder command failed');
//     });