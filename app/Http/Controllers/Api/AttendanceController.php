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
        // $user = Employee::where('emp_code', 'A-2021')->first();

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

        // Loop through each day in the current month correctly
        for ($date = $startDate->copy(); $date->lessThanOrEqualTo($endDate); $date->addDay()) {
            $currentDate = $date->toDateString();
            $dayOfMonth = $date->day;
            $dayOfWeek = $date->format('l');
            $attendance = $attendances->get($currentDate);

            if ($attendance) {
                switch ($attendance->status) {
                    case 'present':
                        $detailedAttendance[] = [
                            'date' => $dayOfMonth,
                            'fdate' => $currentDate,
                            'day' => $dayOfWeek,
                            'status' => 'P',
                            'working_hours' => $attendance->working_hours ?? '--',
                        ];
                        $presentDays++;
                        break;
                    case 'halfday':
                        $detailedAttendance[] = [
                            'date' => $dayOfMonth,
                            'fdate' => $currentDate,
                            'day' => $dayOfWeek,
                            'status' => 'H',
                            'working_hours' => $attendance->working_hours ?? '--',
                        ];
                        $halfDays++;
                        break;
                    case 'weekend':
                        $detailedAttendance[] = [
                            'date' => $dayOfMonth,
                            'day' => $dayOfWeek,
                            'fdate' => $currentDate,
                            'status' => 'WO',
                            'working_hours' => '--',
                        ];
                        break;
                    case 'holiday':
                        $detailedAttendance[] = [
                            'date' => $dayOfMonth,
                            'day' => $dayOfWeek,
                            'fdate' => $currentDate,
                            'status' => 'HO',
                            'working_hours' => '--',
                        ];
                        break;
                    default:
                        $detailedAttendance[] = [
                            'date' => $dayOfMonth,
                            'fdate' => $currentDate,
                            'day' => $dayOfWeek,
                            'status' => 'A',
                            'working_hours' => '--',
                        ];
                        $absentDays++;
                        break;
                }
            } else {
                $detailedAttendance[] = [
                    'date' => $dayOfMonth,
                    'fdate' => $currentDate,
                    'day' => $dayOfWeek,
                    'status' => '--',
                    'working_hours' => '--',
                ];
                $unmarkedDays++;
            }
        }

        $totalAttendance = $presentDays + ($halfDays * 0.5);

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
                $fileName = "{$user->emp_code}_{$date}_punch_in_{$time}." . $request->file('image')->getClientOriginalExtension();
                $imagePath = $request->file('image')->storeAs(
                    "attendance_images/{$user->emp_code}",
                    $fileName,
                    'public'
                );
                $attendance->punch_in_image = $imagePath;
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
                $fileName = "{$user->emp_code}_{$date}_punch_out_{$time}." . $request->file('image')->getClientOriginalExtension();
                $imagePath = $request->file('image')->storeAs(
                    "attendance_images/{$user->emp_code}",
                    $fileName,
                    'public'
                );
                $attendance->punch_out_image = $imagePath;
            }
            $attendance->save();

            return response()->json(['message' => 'Punch Out successful']);
        }

        return response()->json(['message' => 'Invalid punch type'], 400);
    }
}
