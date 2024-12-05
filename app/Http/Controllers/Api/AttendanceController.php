<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
}
