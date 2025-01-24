<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Employee; // Update with your Employee model namespace
use App\Models\Attendance; // Update with your Attendance model namespace

class MarkWeekdays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:markweekdays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark week off (wo) for employees on their specific workdays (e.g., Sat-Sun)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now()->format('D');
        $todayDate = Carbon::now()->format('Y-m-d');
        $employees = Employee::where('is_active', '1')->where('weekdays', 'LIKE', '%' . $today . '%')->get();
        if ($employees->isEmpty()) {
            $this->info("No employees have {$today} as a week off.");
            return Command::SUCCESS;
        }
        foreach ($employees as $employee) {
            $attendance = Attendance::where('emp_code', $employee->emp_code)
                ->where('date', $todayDate)
                ->first();
            if (!$attendance || $attendance->status === null) {
                Attendance::updateOrCreate(
                    ['emp_code' => $employee->emp_code, 'date' => $todayDate],
                    ['status' => 'wo']
                );
                $this->info("Marked week off (wo) for Employee ID {$employee->id} on {$todayDate}.");
            } else {
                $this->info("Attendance already exists for Employee ID {$employee->id} on {$todayDate} with status '{$attendance->status}'.");
            }
        }
        $this->info("Week off (wo) marking completed for {$today}.");
        return Command::SUCCESS;
    }
}
