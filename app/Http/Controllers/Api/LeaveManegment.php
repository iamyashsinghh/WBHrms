<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeaveManegment extends Controller
{
    public function leaveApprovalRequest(Request $request)
    {
        $auth_user = $request->user();
        $approvals = Approval::select('approvals.*', 'employees.name as emp_name')->where('approvals.created_at', '>=', Carbon::now()->subDays(45))
            ->join('employees', 'approvals.emp_code', '=', 'employees.emp_code')
            ->where('employees.reporting_manager', $auth_user->emp_code)
            ->get();
        return response()->json(['approval' => $approvals]);
    }

    public function update_status(Request $request, $id, $status)
    {
        $auth_user = $request->user();
        $auth_name = $auth_user->name;
        $auth_code = $auth_user->emp_code;
        $approval = Approval::findOrFail($id);
        $user = Employee::where('emp_code', $approval->emp_code)->first();

        if ($status == 2) {
            $approval->is_approved = $status;
            $approval->save();
            session()->flash('status', ['success' => true, 'alert_type' => 'error', 'message' => "Approval Rejected."]);
            return redirect()->back();
        } else if ($status == 1) {
            $approval->is_approved = $status;
            $approval->hr_desc = "Approved by $auth_name, Emp Code: $auth_code";
            $approval->save();

            if ($approval->type == 'pl') {
                $start = new DateTime($approval->start);
                $end = ($approval->end) ? new DateTime($approval->end) : $start;

                $period = new DatePeriod(
                    $start,
                    new DateInterval('P1D'),
                    $end->modify('+1 day')
                );

                foreach ($period as $date) {
                    $dateStr = $date->format('Y-m-d');
                    $attendance = Attendance::firstOrNew(['date' => $dateStr, 'emp_code' => $approval->emp_code]);
                    $attendance->status = 'pl';
                    $user->pl_left--;
                    $attendance->save();
                }
            }
            if ($approval->type == 'wo') {
                $start = new DateTime($approval->start);
                $end = ($approval->end) ? new DateTime($approval->end) : $start;

                $period = new DatePeriod(
                    $start,
                    new DateInterval('P1D'),
                    $end->modify('+1 day')
                );

                foreach ($period as $date) {
                    $dateStr = $date->format('Y-m-d');
                    $attendance = Attendance::firstOrNew(['date' => $dateStr, 'emp_code' => $approval->emp_code]);
                    $attendance->status = 'wo';
                    $attendance->save();
                }
            }
            if ($approval->type == 'sl' || $approval->type == 'hd' || $approval->type == 'cl') {
                $attendance = Attendance::firstOrNew(['date' => $approval->start, 'emp_code' => $approval->emp_code]);
                switch ($approval->type) {
                    case 'sl':
                        $attendance->status = 'shortleave';
                        break;
                    case 'hd':
                        $attendance->status = 'halfday';
                        break;
                    case 'cl':
                        $attendance->status = 'cl';
                        $user->cl_left--;
                        break;
                }
                $attendance->save();
                $user->save();
            }
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Approved."]);
            return redirect()->back();
        }
    }

    public function getUsers(Request $request)
    {
        $auth_user = $request->user();
        if ($auth_user->role_id == 6) {
            $users = Employee::where('reporting_manager', $auth_user->emp_code)->get();
            return response()->json(['users' => $users]);
        } else if ($auth_user->role_id == 1) {
            $users = Employee::all();
            return response()->json(['users' => $users]);
        } else if ($auth_user->role_id == 2) {
            $users = Employee::where('role_id', '!=', 1)->get();
            return response()->json(['users' => $users]);
        }
    }

    public function getUser($emp_code)
    {
        $user = Employee::where('emp_code', $emp_code)->first();
        return response()->json(['user' => $user]);
    }

    public function leaveNewApproval(Request $request, $emp_code)
    {
        $status = 1;
        $u = $request->user();
        $user = Employee::where('emp_code', $emp_code)->first();
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
        $approaval->emp_desc = $request->input('emp_desc');

        if ($request->input('type') == 'sl' || $request->input('type') == 'hd') {
            $time = $request->input('time');
            $hr_desc = "Leave at $time";
            $approaval->emp_desc .= $hr_desc;
        } elseif ($request->input('type') == 'pl') {
            $approaval->end = $request->input('end');
            $hr_desc = "From " . $request->input('start') . ' to ' .  $request->input('end');
            $approaval->emp_desc .= $hr_desc;
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
        } else if ($request->input('type') == 'wo') {
            $startInput = $request->input('start');
            $endInput = $request->input('end') ?? $startInput;

            if (!$startInput || !$endInput) {
                return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Start and end dates are required.'], 400);
            }

            $startDate = Carbon::parse($startInput);
            $endDate = Carbon::parse($endInput);
            $approaval->emp_desc .= "Weakoff end date $endInput";

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

        if ($approaval->save()) {
            $auth_user = $request->user();
            $auth_name = $auth_user->name;
            $auth_code = $auth_user->emp_code;
            $approval = Approval::findOrFail($approaval->id);
            $user = Employee::where('emp_code', $approval->emp_code)->first();

            if ($status == 2) {
                $approval->is_approved = $status;
                $approval->save();
                session()->flash('status', ['success' => true, 'alert_type' => 'error', 'message' => "Approval Rejected."]);
                return redirect()->back();
            } else if ($status == 1) {
                $approval->is_approved = $status;
                $approval->hr_desc = "Approved by $auth_name, Emp Code: $auth_code";
                $approval->save();

                if ($approval->type == 'pl') {
                    $start = new DateTime($approval->start);
                    $end = ($approval->end) ? new DateTime($approval->end) : $start;

                    $period = new DatePeriod(
                        $start,
                        new DateInterval('P1D'),
                        $end->modify('+1 day')
                    );

                    foreach ($period as $date) {
                        $dateStr = $date->format('Y-m-d');
                        $attendance = Attendance::firstOrNew(['date' => $dateStr, 'emp_code' => $approval->emp_code]);
                        $attendance->status = 'pl';
                        $user->pl_left--;
                        $attendance->save();
                    }
                }
                if ($approval->type == 'wo') {
                    $start = new DateTime($approval->start);
                    $end = ($approval->end) ? new DateTime($approval->end) : $start;

                    $period = new DatePeriod(
                        $start,
                        new DateInterval('P1D'),
                        $end->modify('+1 day')
                    );

                    foreach ($period as $date) {
                        $dateStr = $date->format('Y-m-d');
                        $attendance = Attendance::firstOrNew(['date' => $dateStr, 'emp_code' => $approval->emp_code]);
                        $attendance->status = 'wo';
                        $attendance->save();
                    }
                }
                if ($approval->type == 'sl' || $approval->type == 'hd' || $approval->type == 'cl') {
                    $attendance = Attendance::firstOrNew(['date' => $approval->start, 'emp_code' => $approval->emp_code]);
                    switch ($approval->type) {
                        case 'sl':
                            $attendance->status = 'shortleave';
                            break;
                        case 'hd':
                            $attendance->status = 'halfday';
                            break;
                        case 'cl':
                            $attendance->status = 'cl';
                            $user->cl_left--;
                            break;
                    }
                    $attendance->save();
                    $user->save();
                }
                session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Approved."]);
                return redirect()->back();
            }
        } else {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Internal Server Error'], 500);
        }
    }
}
