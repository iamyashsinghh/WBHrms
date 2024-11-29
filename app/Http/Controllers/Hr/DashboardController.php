<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
        public function index()
    {
        $auth_user = Auth::guard('hr')->user();
        $empCode = $auth_user->emp_code;

        $total_users = Employee::where('role_id', '!=', 1)->count();

        $month = now()->month;
        $year = now()->year;

        $daysInMonth = Carbon::create($year, $month)->daysInMonth;

        $attendances = Attendance::where('emp_code', $empCode)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->keyBy('date');

        return view('hr.dashboard', compact('total_users', 'daysInMonth', 'month', 'year', 'attendances', 'empCode'));
    }
}
