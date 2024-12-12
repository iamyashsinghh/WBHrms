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
        $presentDays = 0;
        $absentDays = 0;
        $halfDays = 0;
        $unmarkedDays = 0;

        for ($date = $startDate->copy(); $date->lessThanOrEqualTo($endDate); $date->addDay()) {
            $currentDate = $date->toDateString();
            $dayOfMonth = $date->day;
            $dayOfWeek = $date->format('l');
            $attendance = $attendances->get($currentDate);

            if ($attendance) {
                switch ($attendance->status) {
                    case 'present':
                        $presentDays++;
                        break;
                    case 'halfday':
                        $halfDays++;
                        break;
                    case 'absent':
                        $absentDays++;
                        break;
                }
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
                $unmarkedDays++;
            }
        }

        $totalAttendance = $presentDays + ($halfDays * 0.5);
        $todaysAttendance = Attendance::where('emp_code', $user->emp_code)->where('date', Carbon::now()->toDateString())->first();

        return response()->json([
            'user' => $user,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'half_days' => $halfDays,
            'unmarked_days' => $unmarkedDays,
            'total_attendance' => $totalAttendance,
            'detailed_attendance' => $detailedAttendance,
            'month' => $month,
            'year' => $year,
            'today' => $todaysAttendance,
        ]);
    }

    public function mark_attendance(Request $request)
{
    Log::info('Received request:', $request->all());
    $u = $request->user();
    Log::info('Authenticated user:', ['emp_code' => $u->emp_code]);

    $user = Employee::where('emp_code', $u->emp_code)->first();
    if (!$user) {
        Log::error('User not found for emp_code:', ['emp_code' => $u->emp_code]);
        return response()->json(['message' => 'User not found'], 404);
    }
    Log::info('User found:', ['emp_code' => $user->emp_code]);

    $role = Role::find($user->role_id);
    if (!$role) {
        Log::error('Role not found for role_id:', ['role_id' => $user->role_id]);
        return response()->json(['message' => 'Role not found'], 404);
    }
    Log::info('Role found:', ['role_id' => $user->role_id]);

    $today = Carbon::now()->toDateString();
    Log::info('Current date:', ['today' => $today]);

    $attendance = Attendance::where('emp_code', $user->emp_code)
        ->where('date', $today)
        ->first();
    Log::info('Attendance record:', ['attendance' => $attendance]);

    $type = $request->input('type');
    Log::info('Attendance type:', ['type' => $type]);

    $timestamp = $request->input('timestamp');
    Log::info('Received timestamp:', ['timestamp' => $timestamp]);

    try {
        $date = Carbon::parse($timestamp)->toDateString();
        $time = Carbon::parse($timestamp)->format('H:i:s');
        Log::info('Parsed timestamp:', ['date' => $date, 'time' => $time]);
    } catch (\Exception $e) {
        Log::error('Timestamp parsing error:', ['message' => $e->getMessage()]);
        return response()->json(['message' => 'Invalid timestamp format'], 400);
    }

    if ($type == 'Punch In') {
        Log::info('Processing Punch In');

        if ($attendance && $attendance->punch_in_time) {
            Log::warning('Already punched in for today');
            return response()->json(['message' => 'Already punched in for today'], 400);
        }

        if (!$attendance) {
            Log::info('Creating new attendance record for Punch In');
            $attendance = new Attendance();
            $attendance->emp_code = $user->emp_code;
            $attendance->date = $today;
        }

        $attendance->punch_in_time = $time;
        $attendance->punch_in_address = $request->input('address');
        $attendance->punch_in_coordinates = $request->input('coordinates');
        Log::info('Punch In details set');
 Log::info( $user->punch_in_time);
 Log::info( $time);
        // Grace period and lating handling
            $scheduledPunchIn = Carbon::createFromFormat('H:i:s', $user->punch_in_time);
            $actualPunchIn = Carbon::createFromFormat('H:i:s', $time);

            Log::info('Parsed punch-in times', ['scheduled' => $scheduledPunchIn, 'actual' => $actualPunchIn]);

            if ($actualPunchIn->lessThanOrEqualTo($scheduledPunchIn)) {
                $attendance->status = 'present';
            } else {
                $graceEnd = $scheduledPunchIn->copy()->addMinutes($role->grace_time);
                Log::info('Grace end time:', ['graceEnd' => $graceEnd]);

                if ($actualPunchIn->lessThanOrEqualTo($graceEnd)) {
                    $attendance->status = 'present';
                } else {
                    if ($user->latings_left > 0) {
                        $latingEnd = $graceEnd->copy()->addMinutes($role->lating_time);
                        Log::info('Lating end time:', ['latingEnd' => $latingEnd]);

                        if ($actualPunchIn->lessThanOrEqualTo($latingEnd)) {
                            $user->decrement('latings_left');
                            $attendance->status = 'present';
                            Log::info('Late but within allowed period');
                        } else {
                            $attendance->status = 'halfday';
                            Log::warning('Marked as halfday');
                        }
                    } else {
                        $attendance->status = 'halfday';
                        Log::warning('No latings left, marked as halfday');
                    }
                }
            }


        if ($request->hasFile('image')) {
            $file = $request->file('image');
            Log::info('Processing uploaded image');

            if ($file->isValid()) {
                $fileName = "{$user->emp_code}_{$date}_punch_in_{$time}." . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs(
                    "attendance_images/{$user->emp_code}",
                    $fileName,
                    'public'
                );
                $attendance->punch_in_img = $imagePath;
                Log::info('Image saved:', ['path' => $imagePath]);
            } else {
                Log::error('File upload error:', ['message' => $file->getErrorMessage()]);
                return response()->json(['message' => $file->getErrorMessage()], 400);
            }
        }

        $attendance->save();
        Log::info('Punch In saved successfully');
        return response()->json(['message' => 'Punch In successful']);
    }

    if ($type == 'Punch Out') {
        Log::info('Processing Punch Out');

        if ($attendance && $attendance->punch_out_time) {
            Log::warning('Already punched out for today');
            return response()->json(['message' => 'Already punched out for today'], 400);
        }
        if (!$attendance) {
            Log::warning('No Punch In record found for today');
            return response()->json(['message' => 'No Punch In record found for today'], 400);
        }

        $attendance->punch_out_time = $time;
        $attendance->punch_out_address = $request->input('address');
        $attendance->punch_out_coordinates = $request->input('coordinates');
        Log::info('Punch Out details set');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            Log::info('Processing uploaded image for Punch Out');

            if ($file->isValid()) {
                $fileName = "{$user->emp_code}_{$date}_punch_out_{$time}." . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs(
                    "attendance_images/{$user->emp_code}",
                    $fileName,
                    'public'
                );
                $attendance->punch_out_img = $imagePath;
                Log::info('Image saved:', ['path' => $imagePath]);
            } else {
                Log::error('File upload error:', ['message' => $file->getErrorMessage()]);
                return response()->json(['message' => $file->getErrorMessage()], 400);
            }
        }

        // Calculate working hours
        try {
            $punchInDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->date . ' ' . $attendance->punch_in_time);
            $punchOutDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->date . ' ' . $attendance->punch_out_time);
            Log::info('Parsed punch-in and punch-out times', ['in' => $punchInDateTime, 'out' => $punchOutDateTime]);

            $diffInSeconds = $punchInDateTime->diffInSeconds($punchOutDateTime);
            $hours = floor($diffInSeconds / 3600);
            $minutes = floor(($diffInSeconds % 3600) / 60);
            $seconds = $diffInSeconds % 60;
            $workingHours = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

            $attendance->working_hours = $workingHours;
            Log::info('Working hours calculated:', ['working_hours' => $workingHours]);
        } catch (\Exception $e) {
            Log::error('Error in working hours calculation:', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Error in working hours calculation'], 500);
        }

        $attendance->save();
        Log::info('Punch Out saved successfully');
        return response()->json(['message' => 'Punch Out successful']);
    }

    Log::warning('Invalid punch type');
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
