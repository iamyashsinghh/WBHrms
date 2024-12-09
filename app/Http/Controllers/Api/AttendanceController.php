<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
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
        Log::info($request);
        $u = $request->user();
        $user = Employee::where('emp_code', $u->emp_code)->first();
        $today = Carbon::now()->toDateString();
        $attendance = Attendance::where('emp_code', $user->emp_code)
            ->where('date', $today)
            ->first();

        $type = $request->input('type');
        $timestamp = $request->input('timestamp');
        $date = Carbon::parse($timestamp)->toDateString();
        $time = Carbon::parse($timestamp)->format('H:i:s');

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
            $attendance->status = 'present';

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                Log::error('File upload error: ' . $file);
                if (!$file->isValid()) {
                    Log::error('File upload error: ' . $file->getErrorMessage());

                    return response()->json(['message' => $file->getErrorMessage()], 400);
                }
                $fileName = "{$user->emp_code}_{$date}_punch_in_{$time}." . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs(
                    "attendance_images/{$user->emp_code}",
                    $fileName,
                    'public'
                );
                $attendance->punch_in_img = $imagePath;
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
                Log::error('File upload error: ' . $file);
                if (!$file->isValid()) {
                    Log::error('File upload error: ' . $file->getErrorMessage());

                    return response()->json(['message' => $file->getErrorMessage()], 400);
                }
                $fileName = "{$user->emp_code}_{$date}_punch_out_{$time}." . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs(
                    "attendance_images/{$user->emp_code}",
                    $fileName,
                    'public'
                );
                $attendance->punch_out_img = $imagePath;
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

        $date = Carbon::parse($day)->toDateString();

        $attendance = Attendance::where('emp_code', $user->emp_code)
            ->where('date', $date)
            ->first();

        if (!$attendance) {
            return response()->json([
                'message' => 'No attendance record found for this day'
            ], 404);
        }

        $log = [];

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
            'date' => $date,
            'dayOfWeek' => Carbon::parse($date)->format('l'),
            'log' => $log,
        ]);
    }
}
