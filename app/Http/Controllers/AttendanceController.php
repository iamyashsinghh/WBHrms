<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use Auth;

class AttendanceController extends Controller
{
    public function index($emp_code)
    {
        $attendances = Attendance::where('emp_code', $emp_code)->get();
        return view('attendance.index', compact('attendances'));
    }

    public function punchIn(Request $request, $emp_code)
    {
        $today = Carbon::today();

        $attendance = Attendance::firstOrCreate(
            [
                'emp_code' => $emp_code,
                'date' => $today
            ],
            [
                'punch_in_time' => Carbon::now(),
                'status' => 'present'
            ]
        );

        return redirect()->back()->with('success', 'Punch In recorded successfully!');
    }

    public function punchOut(Request $request, $emp_code)
    {
        $today = Carbon::today();

        $attendance = Attendance::where('emp_code', $emp_code)
            ->where('date', $today)
            ->first();

        if ($attendance && $attendance->punch_out_time == null) {
            $attendance->update([
                'punch_out_time' => Carbon::now(),
            ]);
            return redirect()->back()->with('success', 'Punch Out recorded successfully!');
        }

        return redirect()->back()->with('error', 'Punch Out already recorded or not punched in.');
    }
}
