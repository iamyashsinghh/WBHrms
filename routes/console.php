<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Artisan::command('app:send-daily-notification-to-user-for-attendance', function(){
    \Illuminate\Support\Facades\Artisan::call('app:send-daily-notification-to-user-for-attendance');
})->purpose('Send FCM Notification to employees')->everyMinute();
