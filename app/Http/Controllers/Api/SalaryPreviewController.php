<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\SalarySlip;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalaryPreviewController extends Controller
{
    public function get_salary(Request $request, $month, $year)
    {
        $u = $request->user();
        $salmonth = $month ?? now()->month;
        $salyear = $year ?? now()->year;

        $user = Employee::where('emp_code', $u->emp_code)->first();
        if ($user->emp_type === 'Fulltime') {
            $startDate = Carbon::create($salyear, $salmonth, 15)->subMonth();
            $endDate = Carbon::create($salyear, $salmonth, 14);
        } else {
            $startDate = Carbon::create($salyear, $salmonth, 1);
            $endDate = Carbon::create($salyear, $salmonth)->endOfMonth();
        }


        $attendances = Attendance::where('emp_code', $user->emp_code)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $present = Attendance::where('emp_code', $user->emp_code)->where('status', 'present')
        ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])->count();

        $absent = Attendance::where('emp_code', $user->emp_code)->where('status', 'absent')
        ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])->count();

        $wo = Attendance::where('emp_code', $user->emp_code)->where('status', 'wo')
        ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])->count();

        $holiday = Attendance::where('emp_code', $user->emp_code)->where('status', 'holiday')
        ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])->count();

        $halfday = Attendance::where('emp_code', $user->emp_code)->where('status', 'halfday')
        ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])->count();

        $cl = Attendance::where('emp_code', $user->emp_code)->where('status', 'cl')
        ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])->count();

        $pl = Attendance::where('emp_code', $user->emp_code)->where('status', 'pl')
        ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])->count();

        $shortleave = Attendance::where('emp_code', $user->emp_code)->where('status', 'shortleave')
        ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])->count();

        $unmarked = 30 - $attendances->count();
        $salary_slip = SalarySlip::where(['emp_code'=> $user->emp_code, 'month'=> $month, 'year' => $year])->first();
        return response()->json([
            'attendances' => $attendances,
            'absent' => $absent,
            'wo' => $wo,
            'holiday' => $holiday,
            'halfday' => $halfday,
            'cl' => $cl,
            'pl' => $pl,
            'present' => $present,
            'shortleave' => $shortleave,
            'unmarked' => $unmarked,
            'salary_slip' =>$salary_slip,
        ]);
    }
}
