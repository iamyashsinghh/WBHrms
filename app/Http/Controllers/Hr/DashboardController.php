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
    public function index(){

        $total_users = Employee::count();
        $month = now()->month;
        $year = now()->year;

        $preDayAttendance = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = Attendance::where('date', 'like', "%$datetime%")->count();
            array_push($preDayAttendance, $count);
        }
        $preDayAttendance = implode(",", $preDayAttendance);

        $preDayAttendancePresent = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = Attendance::where('date', 'like', "%$datetime%")->where('status', 'present')->count();
            array_push($preDayAttendancePresent, $count);
        }
        $preDayAttendancePresent = implode(",", $preDayAttendancePresent);

        $preDayAttendanceAbsent = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = Attendance::where('date', 'like', "%$datetime%")->where('status', 'absent')->count();
            array_push($preDayAttendanceAbsent, $count);
        }
        $preDayAttendanceAbsent = implode(",", $preDayAttendanceAbsent);

        $preDayAttendanceHalfday = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = Attendance::where('date', 'like', "%$datetime%")->where('status', 'halfday')->count();
            array_push($preDayAttendanceHalfday, $count);
        }
        $preDayAttendanceHalfday = implode(",", $preDayAttendanceHalfday);

        $preDayAttendanceWo = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = Attendance::where('date', 'like', "%$datetime%")->where('status', 'wo')->count();
            array_push($preDayAttendanceWo, $count);
        }
        $preDayAttendanceWo = implode(",", $preDayAttendanceWo);


        return view('hr.dashboard', compact('total_users', 'month', 'year', 'preDayAttendance', 'preDayAttendancePresent', 'preDayAttendanceAbsent', 'preDayAttendanceHalfday','preDayAttendanceWo'));
    }
}
