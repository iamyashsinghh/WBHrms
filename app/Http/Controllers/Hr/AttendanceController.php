<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function fetchAttendance(Request $request)
    {
        $month = $request->input('month') ?? now()->month;
        $year = $request->input('year') ?? now()->year;

        $employees = Employee::all();
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;

        $attendanceData = [];

        foreach ($employees as $employee) {
            if ($employee->emp_type === 'Fulltime') {
                $startDate = Carbon::create($year, $month, 15)->subMonth();
                $endDate = Carbon::create($year, $month, $daysInMonth);
                $daysToLoop = $startDate->diffInDays($endDate) + 1;
            } else {
                $startDate = Carbon::create($year, $month, 1);
                $endDate = Carbon::create($year, $month, $daysInMonth);
                $daysToLoop = $daysInMonth;
            }

            $attendances = Attendance::where('emp_code', $employee->emp_code)
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->get()
                ->keyBy('date');

            $detailedAttendance = [];
            for ($i = 0; $i < $daysToLoop; $i++) {
                $currentDate = $startDate->copy()->addDays($i)->toDateString();
                $attendance = $attendances->get($currentDate);

                if ($attendance) {
                    $detailedAttendance[$currentDate] = [
                        'status' => match ($attendance->status) {
                            'present' => 'P',
                            'weekend' => 'WO',
                            'holiday' => 'HO',
                            'halfday' => 'H',
                            default => 'A',
                        },
                        'working_hours' => $attendance->working_hours ?? '--',
                    ];
                } else {
                    $detailedAttendance[$currentDate] = [
                        'status' => '--',
                        'working_hours' => '--',
                    ];
                }
            }

            $attendanceData[] = [
                'employee' => $employee,
                'detailed_attendance' => $detailedAttendance,
            ];
        }
        return response()->json([
            'attendanceData' => $attendanceData,
            'daysInMonth' => $daysInMonth,
            'month' => $month,
            'year' => $year,
        ]);
    }
}
