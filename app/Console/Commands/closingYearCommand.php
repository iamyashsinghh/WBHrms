<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;

class closingYearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:closing-year-command';

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
        $now = Carbon::now();
        $customAccountsClosingYear = Carbon::create($now->year, 3, 31);
        if($now == $customAccountsClosingYear){
            $users = Employee::where('status', 'FullTime')->where('is_active', 1)->get();
            foreach($users as $user){
                $user->cl_left = 12;
                $customAccountsStartingYear = Carbon::create($now->year, 4, 1);
                $totalPl = Attendance::where('emp_code', $user->emp_code)->where('status', 'pl')
                ->whereBetween('date', [$customAccountsStartingYear->toDateString(), $customAccountsClosingYear->toDateString()])->count();
                if($totalPl >= 10){
                    $user->pl_left = $totalPl/2 + 10;
                }else{
                    $user->pl_left = 10;
                }
                $user->save();
            }
        }
    }
}
