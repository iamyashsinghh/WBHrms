<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApprovalController extends Controller
{

    public function clApprovalDates(Request $request,  $month, $year){
        $u = $request->user();
        $user = Employee::where('emp_code', $u->emp_code)->first();

        $startDate = Carbon::create($year, $month, 1);
        $startDate = Carbon::create($year, $month, 15)->subMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        $attendances = Attendance::where('emp_code', $user->emp_code)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->keyBy('date');

        $detailedAttendance = [];

        for ($date = $startDate->copy(); $date->lessThanOrEqualTo($endDate); $date->addDay()) {
            $currentDate = $date->toDateString();
            $dayOfMonth = $date->day;
            $dayOfWeek = $date->format('l');
            $attendance = $attendances->get($currentDate);
            if ($attendance) {
                $detailedAttendance[] = [
                    'date' => $dayOfMonth,
                    'todaydate' => $currentDate,
                    'day' => $dayOfWeek,
                    'status' => $attendance->status,
                    'punch_in_time' => $attendance->punch_in_time,
                    'punch_out_time' => $attendance->punch_out_time,
                    'working_hours' => $attendance->working_hours ?? '--',
                ];
            } else {
                $detailedAttendance[] = [
                    'date' => $dayOfMonth,
                    'todaydate' => $currentDate,
                    'day' => $dayOfWeek,
                    'status' => 'unmarked',
                    'punch_in_time' => null,
                    'punch_out_time' => null,
                    'working_hours' => '--',
                ];
            }
        }

        $todaysAttendance = Attendance::where('emp_code', $user->emp_code)->where('date', Carbon::now()->toDateString())->first();

        return response()->json([
            'user' => $user,
            'detailed_attendance' => $detailedAttendance,
            'month' => $month,
            'year' => $year,
            'today' => $todaysAttendance,
        ]);
    }

    public function newApproval(Request $request){
        $u = $request->user();
        $user = Employee::where('emp_code', $u->emp_code)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validate = Validator::make($request->all(), [
            'type' => "required|string",
            'start' => "required",
            'emp_desc' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'All feilds are required.'], 400);
        }

        $approaval = new Approval();
        $approaval->emp_code = $user->emp_code;
        $approaval->type = $request->input('type');
        $approaval->start = $request->input('start');

        if ($request->input('type') == 'sl' || $request->input('type') == 'hd') {
            $time = $request->input('time');
            $emp_desc = "I will leave at $time";
            $emp_desc .= "\n" . $request->input('emp_desc');
            $approaval->emp_desc = $emp_desc;
        }elseif($request->input('type') == 'pl'){
            $approaval->end = $request->input('end');
            $emp_desc = $request->input('emp_desc');
            $emp_desc .= "\n  From " . $request->input('start') .' to '.  $request->input('end');
            $approaval->emp_desc = $emp_desc;
        }else{
            $approaval->emp_desc = $request->input('emp_desc');
        }

        if($approaval->save()){
            return response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Applied Successfully Please wait For Approval.'], 200);
        }else{
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Internal Server Error'], 500);
        }
    }
}
