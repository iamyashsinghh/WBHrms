<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function fetchUserAttendance(Request $request)
    {
        $month = $request->input('month') ?? now()->month;
        $year = $request->input('year') ?? now()->year;

        // Get the authenticated user
        $user = $request->user();

        // Determine start and end dates based on user type (Fulltime or others)
        if ($user->emp_type === 'Fulltime') {
            $startDate = Carbon::create($year, $month, 15)->subMonth();
            $endDate = Carbon::create($year, $month, 14);
        } else {
            $startDate = Carbon::create($year, $month, 1);
            $endDate = Carbon::create($year, $month)->endOfMonth();
        }

        // Fetch attendance records for the user within the defined range
        $attendances = Attendance::where('emp_code', $user->emp_code)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->keyBy('date');

        $detailedAttendance = [];
        $presentDays = 0;
        $absentDays = 0;
        $halfDays = 0;
        $unmarkedDays = 0;

        // Generate attendance details day by day
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $currentDate = $date->toDateString();
            $attendance = $attendances->get($currentDate);

            if ($attendance) {
                switch ($attendance->status) {
                    case 'present':
                        $detailedAttendance[$currentDate] = [
                            'status' => 'P',
                            'working_hours' => $attendance->working_hours ?? '--',
                        ];
                        $presentDays++;
                        break;
                    case 'halfday':
                        $detailedAttendance[$currentDate] = [
                            'status' => 'H',
                            'working_hours' => $attendance->working_hours ?? '--',
                        ];
                        $halfDays++;
                        break;
                    case 'weekend':
                        $detailedAttendance[$currentDate] = [
                            'status' => 'WO',
                            'working_hours' => '--',
                        ];
                        break;
                    case 'holiday':
                        $detailedAttendance[$currentDate] = [
                            'status' => 'HO',
                            'working_hours' => '--',
                        ];
                        break;
                    default:
                        $detailedAttendance[$currentDate] = [
                            'status' => 'A',
                            'working_hours' => '--',
                        ];
                        $absentDays++;
                        break;
                }
            } else {
                $detailedAttendance[$currentDate] = [
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
