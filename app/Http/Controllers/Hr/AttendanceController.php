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
        $attendanceData = [];
        foreach ($employees as $employee) {
            if ($employee->emp_type === 'Fulltime') {
                $startDate = Carbon::create($year, $month, 15)->subMonth();
                $endDate = Carbon::create($year, $month)->endOfMonth();
            } else {
                $startDate = Carbon::create($year, $month, 1);
                $endDate = Carbon::create($year, $month)->endOfMonth();
            }
            $attendances = Attendance::where('emp_code', $employee->emp_code)
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->get()
                ->keyBy('date');
            $detailedAttendance = [];
            $presentDays = 0;
            $absentDays = 0;
            $halfDays = 0;
            $unmarkedDays = 0;
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
                            break;
                        case 'halfday':
                            $detailedAttendance[$currentDate] = [
                                'status' => 'H',
                                'working_hours' => $attendance->working_hours ?? '--',
                            ];
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
                            break;
                    }
                } else {
                    $detailedAttendance[$currentDate] = [
                        'status' => '--',
                        'working_hours' => '--',
                    ];
                }
            }
            if ($employee->emp_type === 'Fulltime') {
                $calcStartDate = Carbon::create($year, $month, 15)->subMonth();
                $calcEndDate = Carbon::create($year, $month, 14);
            } else {
                $calcStartDate = Carbon::create($year, $month, 1);
                $calcEndDate = Carbon::create($year, $month)->endOfMonth();
            }
            for ($date = $calcStartDate->copy(); $date <= $calcEndDate; $date->addDay()) {
                $currentDate = $date->toDateString();
                $attendance = $attendances->get($currentDate);
                if ($attendance) {
                    switch ($attendance->status) {
                        case 'present':
                            $presentDays++;
                            break;
                        case 'halfday':
                            $halfDays++;
                            break;
                        case 'weekend':
                        case 'holiday':
                            break;
                        default:
                            $absentDays++;
                            break;
                    }
                } else {
                    $unmarkedDays++;
                }
            }
            $totalAttendance = $presentDays + ($halfDays * 0.5);
            $attendanceData[] = [
                'employee' => $employee,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'half_days' => $halfDays,
                'unmarked_days' => $unmarkedDays,
                'total_attendance' => $totalAttendance,
                'detailed_attendance' => $detailedAttendance,
            ];
        }
        return response()->json([
            'attendanceData' => $attendanceData,
            'month' => $month,
            'year' => $year,
        ]);
    }

    public function get_attendance($emp_code, $date)
    {
        $attendance = Attendance::where('emp_code', $emp_code)->whereDate('date', $date)->first();

        if ($attendance) {
            return response()->json([
                'success' => true,
                'attendance' => [
                    'status' => $attendance->status,
                    'punch_in_time' => $attendance->punch_in_time,
                    'punch_out_time' => $attendance->punch_out_time,
                    'working_hours' => $attendance->working_hours,
                    'desc' => $attendance->desc,
                ],
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Attendance record not found. You can create a new one.',
            ]);
        }
    }

    public function store_attendance($emp_code, $date, Request $request)
    {
        if ($request->punch_in_time) {
            $request->merge([
                'punch_in_time' => substr($request->punch_in_time, 0, 5),
            ]);
        }

        if ($request->punch_out_time) {
            $request->merge([
                'punch_out_time' => substr($request->punch_out_time, 0, 5),
            ]);
        }
        
        $request->validate([
            'status' => 'required|string',
            'punch_in_time' => 'nullable|date_format:H:i',
            'punch_out_time' => 'nullable|date_format:H:i|after:punch_in_time',
            'desc' => 'nullable',
        ]);

        $workingHours = null;
        if ($request->punch_in_time && $request->punch_out_time) {
            $start = \Carbon\Carbon::createFromFormat('H:i', $request->punch_in_time);
            $end = \Carbon\Carbon::createFromFormat('H:i', $request->punch_out_time);
            $workingHours = $start->diff($end)->format('%H:%I');
        }

        $attendance = Attendance::updateOrCreate(
            [
                'emp_code' => $emp_code,
                'date' => $date,
            ],
            [
                'status' => $request->status,
                'desc' => $request->status,
                'punch_in_time' => $request->punch_in_time,
                'punch_out_time' => $request->punch_out_time,
                'working_hours' => $workingHours,
            ]
        );

        if ($attendance) {
            return response()->json([
                'success' => true,
                'message' => 'Attendance updated successfully.',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update attendance.',
            ]);
        }
    }
}
