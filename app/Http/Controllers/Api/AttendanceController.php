<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function fetchUserAttendance(Request $request, $month, $year)
    {
        $u = $request->user();
        $user = Employee::where('emp_code', $u->emp_code)->first();

        $startDate = Carbon::create($year, $month, 1);
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
        $present = Attendance::where('emp_code', $user->emp_code)->where('status', 'present')
        ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])->count();

        $absent = Attendance::where('emp_code', $user->emp_code)->where('status', 'absent')
        ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])->count();
        $halfday = Attendance::where('emp_code', $user->emp_code)->where('status', 'halfday')
        ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])->count();


        return response()->json([
            'detailed_attendance' => $detailedAttendance,
            'today' => $todaysAttendance,
            'user' => $user,
            'present' => $present,
            'absent' => $absent,
            'halfday' => $halfday,
        ]);
    }

    public function mark_attendance(Request $request)
    {
        $u = $request->user();
        $user = Employee::where('emp_code', $u->emp_code)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $role = Role::find($user->role_id);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }
        $today = Carbon::now()->toDateString();
        $attendance = Attendance::where('emp_code', $user->emp_code)
            ->where('date', $today)
            ->first();

        $type = $request->input('type');
        $currentDateTime = Carbon::now();
        $date = $currentDateTime->toDateString();
        $time = $currentDateTime->format('H:i:s');
        if ($type == 'Punch In') {
            if ($attendance && $attendance->punch_in_time) {
                return response()->json(['message' => 'Already punched in for today'], 400);
            }
            if (!$attendance) {
                $attendance = new Attendance();
                $attendance->emp_code = $user->emp_code;
                $attendance->date = $today;
            }
            $attendance->punch_in_time = $time;
            $attendance->punch_in_address = $request->input('address');
            $attendance->punch_in_coordinates = $request->input('coordinates');
            $scheduledPunchIn = Carbon::createFromFormat('H:i:s', $user->punch_in_time);
            $actualPunchIn = Carbon::createFromFormat('H:i:s', $time);
            $graceTime = Carbon::parse($role->grace_time)->minute;
            $latingTime = Carbon::parse($role->lating_time)->minute;
            $graceEnd = $scheduledPunchIn->copy()->addMinutes($graceTime);
            $latingEnd = $graceEnd->copy()->addMinutes($latingTime);
            if ($actualPunchIn->lessThanOrEqualTo($scheduledPunchIn)) {
                $attendance->status = 'present';
            } elseif ($actualPunchIn->lessThanOrEqualTo($graceEnd)) {
                $attendance->status = 'present';
            } elseif ($actualPunchIn->lessThanOrEqualTo($latingEnd)) {
                if ($user->latings_left > 0) {
                    $user->decrement('latings_left');
                    $attendance->status = 'present';
                } else {
                    $attendance->status = 'halfday';
                }
            } else {
                $attendance->status = 'halfday';
            }
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                if ($file->isValid()) {
                    $fileName = "{$user->emp_code}_{$date}_punch_in_{$time}." . $file->getClientOriginalExtension();
                    $imagePath = $file->storeAs(
                        "attendance_images/{$user->emp_code}",
                        $fileName,
                        'public'
                    );
                    $attendance->punch_in_img = $imagePath;
                } else {
                    return response()->json(['message' => $file->getErrorMessage()], 400);
                }
            }
            $attendance->save();
            return response()->json(['message' => 'Punch In successful']);
        }
        if ($type == 'Punch Out') {
            if ($attendance && $attendance->punch_out_time) {
                return response()->json(['message' => 'Already punched out for today'], 400);
            }
            if (!$attendance) {
                return response()->json(['message' => 'No Punch In record found for today'], 400);
            }

            $attendance->punch_out_time = $time;
            $attendance->punch_out_address = $request->input('address');
            $attendance->punch_out_coordinates = $request->input('coordinates');

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                if ($file->isValid()) {
                    $fileName = "{$user->emp_code}_{$date}_punch_out_{$time}." . $file->getClientOriginalExtension();
                    $imagePath = $file->storeAs(
                        "attendance_images/{$user->emp_code}",
                        $fileName,
                        'public'
                    );
                    $attendance->punch_out_img = $imagePath;
                } else {
                    return response()->json(['message' => $file->getErrorMessage()], 400);
                }
            }
            try {
                $punchInDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->date . ' ' . $attendance->punch_in_time);
                $punchOutDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->date . ' ' . $attendance->punch_out_time);
                $diffInSeconds = $punchInDateTime->diffInSeconds($punchOutDateTime);
                $hours = floor($diffInSeconds / 3600);
                $minutes = floor(($diffInSeconds % 3600) / 60);
                $seconds = $diffInSeconds % 60;
                $workingHours = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                $attendance->working_hours = $workingHours;
                $scheduledPunchOut = Carbon::createFromFormat('H:i:s', $user->punch_out_time);
                $totalExpectedSeconds = $scheduledPunchOut->diffInSeconds($punchInDateTime);
                if ($diffInSeconds < $totalExpectedSeconds && $attendance->status === 'present') {
                    $attendance->status = 'halfday';
                }
                if($user->type == 'Fulltime'){
                    $now = Carbon::now();
                    $customDate = Carbon::create($now->year, $now->month, 14);
                    if ($attendance->date === $customDate) {
                        // we can use some logics here
                    }
                }else{
                    $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
                    if ($attendance->date === $endOfMonth) {
                        // we can use some logics here
                    }
                }
                $now = Carbon::now();
                $customAccountsClosingYear = Carbon::create($now->year, 3, 31);
                if ($attendance->date === $customAccountsClosingYear) {
                    $user->cl_left = 12;
                    $customAccountsStartingYear = Carbon::create($now->year, 4, 1);
                    $totalPl = Attendance::where('emp_code', $user->emp_code)->where('status', 'pl')
                    ->whereBetween('date', [$customAccountsStartingYear->toDateString(), $customAccountsClosingYear->toDateString()])->count();
                    if($totalPl >= 10){
                        $user->pl_left = $totalPl/2 + 10;
                    }
                    $user->save();
                }
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error in working hours calculation'], 500);
            }
            $attendance->save();
            return response()->json(['message' => 'Punch Out successful']);
        }
        return response()->json(['message' => 'Invalid punch type'], 400);
    }

    public function fetchDayAttendanceLog(Request $request, $day)
    {
        $u = $request->user();
        $user = Employee::where('emp_code', $u->emp_code)->first();

        $date = Carbon::parse($day);

        $attendance = Attendance::where('emp_code', $user->emp_code)
            ->where('date', $date)
            ->first();

        $log = [];

        if (!$attendance) {
            return response()->json([
                'date' => $date->toDateString(),
                'dayOfWeek' => $date->format('l'),
                'status' => 'unmarked',
                'log' => [],
            ]);
        }

        if ($attendance->punch_in_time) {
            $log[] = [
                'action' => 'Punched In',
                'time' => $attendance->punch_in_time,
                'address' => $attendance->punch_in_address,
                'coordinates' => $attendance->punch_in_coordinates,
                'image' => $attendance->punch_in_img
                    ? asset('storage/' . $attendance->punch_in_img)
                    : null,
            ];
        }

        if ($attendance->punch_out_time) {
            $log[] = [
                'action' => 'Punched Out',
                'time' => $attendance->punch_out_time,
                'address' => $attendance->punch_out_address,
                'coordinates' => $attendance->punch_out_coordinates,
                'image' => $attendance->punch_out_img
                    ? asset('storage/' . $attendance->punch_out_img)
                    : null,
            ];
        }

        return response()->json([
            'date' => $date->toDateString(),
            'dayOfWeek' => Carbon::parse($date->toDateString())->format('l'),
            'log' => $log,
        ]);
    }
}
