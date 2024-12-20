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

    public function clApprovalDates(Request $request,  $month, $year)
    {
        $u = $request->user();
        $user = Employee::where('emp_code', $u->emp_code)->first();

        if ($user->emp_type === 'Fulltime') {
            $startDate = Carbon::create($year, $month, 15)->subMonth();
            $endDate = Carbon::create($year, $month)->endOfMonth();
        } else {
            $startDate = Carbon::create($year, $month, 1);
            $endDate = Carbon::create($year, $month)->endOfMonth();
        }

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

    public function newApproval(Request $request)
    {
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
            $hr_desc = "Leave at $time";
            $approaval->hr_desc = $hr_desc;
        } elseif ($request->input('type') == 'pl') {
            $approaval->end = $request->input('end');
            $hr_desc = "From " . $request->input('start') . ' to ' .  $request->input('end');
            $approaval->hr_desc = $hr_desc;
        } elseif ($request->input('type') == 'cl') {
            $startInput = $request->input('start');

            if ($user->emp_type == 'Fulltime') {
                $startDate = Carbon::create(now()->year, now()->month, 15)->subMonth();
                $endDate = Carbon::create(now()->year, now()->month, 14);
            } else {
                $startDate = Carbon::create(now()->year, now()->month, 1);
                $endDate = Carbon::create(now()->year, now()->month)->endOfMonth();
            }
            if ($startInput && Carbon::parse($startInput)->gt($endDate)) {
                if ($user->emp_type == 'Fulltime') {
                    $startDate = Carbon::create(now()->year, now()->month, 15);
                    $endDate = Carbon::create(now()->year, now()->month + 1, 14);
                } else {
                    $startDate = Carbon::create(now()->year, now()->month + 1, 1);
                    $endDate = Carbon::create(now()->year, now()->month + 1)->endOfMonth();
                }
            }
            $attendance_count = Attendance::where('status', 'cl')->where('emp_code', $user->emp_code)->whereBetween('date', [$startDate, $endDate])->count();
            if ($attendance_count > 0) {
                $hr_desc = "Already $attendance_count CL is marked between $startDate and $endDate.\n";
                return response()->json(['success' => false, 'alert_type' => 'success', 'message' => $hr_desc], 200);
            }
        }elseif ($request->input('type') == 'wo') {
            $startInput = $request->input('start');
            $endInput = $request->input('end');

            if (!$startInput || !$endInput) {
                return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Start and end dates are required.'], 400);
            }

            $startDate = Carbon::parse($startInput);
            $endDate = Carbon::parse($endInput);
            $approaval->hr_desc = "Weakoff end date $endInput";

            if ($user->emp_type == 'Fulltime') {
                $cycleStart = Carbon::create(now()->year, now()->month, 15)->subMonth();
                $cycleEnd = Carbon::create(now()->year, now()->month, 14);
            } else {
                $cycleStart = Carbon::create(now()->year, now()->month, 1);
                $cycleEnd = Carbon::create(now()->year, now()->month)->endOfMonth();
            }

            if ($startDate->gt($cycleEnd)) {
                if ($user->emp_type == 'Fulltime') {
                    $cycleStart = Carbon::create(now()->year, now()->month, 15);
                    $cycleEnd = Carbon::create(now()->year, now()->month + 1, 14);
                } else {
                    $cycleStart = Carbon::create(now()->year, now()->month + 1, 1);
                    $cycleEnd = Carbon::create(now()->year, now()->month + 1)->endOfMonth();
                }
            }
            $attendance_count = Attendance::where('status', 'wo')
                ->where('emp_code', $user->emp_code)
                ->whereBetween('date', [$cycleStart, $cycleEnd])
                ->count();

            $requestedDays = $startDate->diffInDays($endDate) + 1;

            if ($attendance_count + $requestedDays > 4) {
                $allowedDays = 4 - $attendance_count;
                $hr_desc = "You have already marked $attendance_count weak off(s) between $cycleStart and $cycleEnd. Only $allowedDays day(s) remaining for this cycle.";
                return response()->json(['success' => false, 'alert_type' => 'error', 'message' => $hr_desc], 200);
            }
            $approaval->end = $endInput;
        }
        $approaval->emp_desc = $request->input('emp_desc');

        if ($approaval->save()) {
            return response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Applied Successfully Please wait For Approval.'], 200);
        } else {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Internal Server Error'], 500);
        }
    }

    public function getApprovals(Request $request)
    {
        $u = $request->user();
        $user = Employee::where('emp_code', $u->emp_code)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);

        $approvals = Approval::where('emp_code', $user->emp_code)->skip($offset)->take($limit)->get();

        return response()->json([
            'success' => true,
            'data' => $approvals
        ], 200);
    }
}
