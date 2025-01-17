<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SendDailyNotificationToUserForAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-notification-to-user-for-attendance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now()->format('H:i');
        Log::info($now);
        $employees = Employee::whereNotNull('notification_token')->whereRaw("DATE_FORMAT(punch_in_time, '%H:%i') = ?", [$now])->get();
        Log::info($employees);
        foreach ($employees as $employee) {
            $response = sendFCMNotification(
                $employee->notification_token,
                "Attendance Reminder",
                "Hello {$employee->name}, it's time to punch in.",
                ['employee_id' => $employee->id],
                'https://wbcrm.in/wb-logo2.webp'
            );
        }
    }
}
