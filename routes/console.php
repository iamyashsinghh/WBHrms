<?php
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Schedule::command('app:send-daily-notification-to-user-for-attendance')->everyMinute();
Schedule::command('app:sandwichpolicy')->cron('0 15 * * 2'); // run only at a specific time (3:00 PM) on Tuesdays
Schedule::command('app:markweekdays')->cron('0 23 * * 6,0'); // run only at sat sunday
