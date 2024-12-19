<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
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

        return response()->json([
            'attendances' => $attendances,
        ]);
    }
}
