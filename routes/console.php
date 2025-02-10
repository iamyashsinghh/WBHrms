<?php
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:send-daily-notification-to-user-for-attendance')->everyMinute();
Schedule::command('app:sandwichpolicy')->cron('0 15 * * 2'); // run only at a specific time (3:00 PM) on Tuesdays
Schedule::command('app:markweekdays')->cron('0 23 * * 6,0'); // run only at sat sunday
Schedule::command('app:closing-year-command')->cron('55 23 31 3 *'); // Runs at 11:55 PM on March 31st every year
Schedule::command('app:lating-reset-command')->daily();
Schedule::command('app:mark-holiday-command')->daily();
