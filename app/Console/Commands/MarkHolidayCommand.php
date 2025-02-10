<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkHolidayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mark-holiday-command';

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
        $todayDate = Carbon::now()->format('Y-m-d');
        $holidy = Holiday::where('date', $todayDate)->get();
        if ($holidy->isEmpty()) {
            return Command::SUCCESS;
        }
        $employees = Employee::where('is_active', '1')->whereIn('role_id', [1, 2, 3])->get();
        if ($employees->isEmpty()) {
            return Command::SUCCESS;
        }
        foreach ($employees as $employee) {
            $attendance = Attendance::where('emp_code', $employee->emp_code)
                ->where('date', $todayDate)
                ->first();
            if (!$attendance || $attendance->status === null) {
                Attendance::updateOrCreate(
                    ['emp_code' => $employee->emp_code, 'date' => $todayDate],
                    ['status' => 'holiday']
                );
            }
        }
        return Command::SUCCESS;
    }
}
