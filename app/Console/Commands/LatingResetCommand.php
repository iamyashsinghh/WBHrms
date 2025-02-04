<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;

class LatingResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:lating-reset-command';

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
        $users = Employee::where('status', 'FullTime')->where('is_active', 1)->get();
        foreach ($users as $user) {
            if ($user->type == 'Fulltime') {
                $now = Carbon::now();
                $customDate = Carbon::create($now->year, $now->month, 14);
                if ($now->isSameDay($customDate)) {
                    $user->lating_left = 3;
                }
            } else {
                $now = Carbon::now();
                $endOfMonth = Carbon::now()->endOfMonth();
                if ($now->isSameDay($endOfMonth)) {
                    $user->lating_left = 3;
                }
            }
            $user->save();
        }
    }
}
