<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function dailyAttendancePage()
    {
        return view('admin.attendance.daily');
    }
    public function fetchDailyAttendance(Request $request)
    {
        $date = $request->input('date') ?? now()->toDateString();

        $employees = Employee::all();
        $attendanceData = [];

        foreach ($employees as $employee) {
            $attendance = Attendance::where('emp_code', $employee->emp_code)
                ->whereDate('date', $date)
                ->first();

            $attendanceData[] = [
                'employee' => $employee,
                'status' => $attendance->status ?? '--',
                'working_hours' => $attendance->working_hours ?? '--',
                'punch_in_time' => isset($attendance->punch_in_time) ? Carbon::parse($attendance->punch_in_time)->format('h:i a') : '--',
                'punch_out_time' => isset($attendance->punch_out_time) ? Carbon::parse($attendance->punch_out_time)->format('h:i a') : '--',
            ];
        }

        return response()->json([
            'attendanceData' => $attendanceData,
        ]);
    }

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
                        case 'wo':
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
                        case 'cl':
                            $detailedAttendance[$currentDate] = [
                                'status' => 'CL',
                                'working_hours' => '--',
                            ];
                            break;
                        case 'pl':
                            $detailedAttendance[$currentDate] = [
                                'status' => 'PL',
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
                        case 'absent':
                            $absentDays++;
                            break;
                        case 'wo':
                        case 'holiday':
                        case 'cl':
                        case 'pl':
                        case 'shortleave':
                            break;
                    }
                } else {
                    $unmarkedDays++;
                }
            }
            $totalAttendance = 30 - $halfDays / 2 - $absentDays - $unmarkedDays;
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
                'attendance' => $attendance,
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

    public function downloadAttendance(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $format = $request->input('format');
        $month = $request->input('month');
        $year = $request->input('year');

        $employee = Employee::find($employeeId);
        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // Validate month and year
        if (!$month || !$year) {
            return response()->json(['error' => 'Invalid month or year'], 400);
        }

        if ($employee->emp_type === 'Fulltime') {
            $startDate = Carbon::create($year, $month, 15)->subMonth();
            $endDate = Carbon::create($year, $month, 14);
        } else {
            $startDate = Carbon::create($year, $month, 1);
            $endDate = Carbon::create($year, $month)->endOfMonth();
        }

        $attendances = Attendance::where('emp_code', $employee->emp_code)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->keyBy('date');

        $detailedAttendance = [];
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $currentDate = $date->toDateString();
            $attendance = $attendances->get($currentDate);
            $detailedAttendance[$currentDate] = [
                'status' => $attendance->status ?? '--',
                'working_hours' => $attendance->working_hours ?? '--',
                'punch_in_time' => isset($attendance->punch_in_time) ? Carbon::parse($attendance->punch_in_time)->format('h:i a') : '--',
                'punch_out_time' => isset($attendance->punch_out_time) ? Carbon::parse($attendance->punch_out_time)->format('h:i a') : '--',
            ];
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.pdf.attendance', compact('employee', 'detailedAttendance'));
            $filePath = storage_path("app/public/attendance-{$employeeId}.pdf");
            $pdf->save($filePath);
        } elseif ($format === 'excel') {
            try {
                // Generate Excel
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // Set headers
                $sheet->setCellValue('A1', 'Date');
                $sheet->setCellValue('B1', 'Status');
                $sheet->setCellValue('C1', 'Working Hours');
                $sheet->setCellValue('D1', 'Punch In Time');
                $sheet->setCellValue('E1', 'Punch Out Time');

                // Populate data
                $row = 2;
                foreach ($detailedAttendance as $date => $details) {
                    $sheet->setCellValue("A{$row}", $date);
                    $sheet->setCellValue("B{$row}", ucfirst($details['status']));
                    $sheet->setCellValue("C{$row}", $details['working_hours']);
                    $sheet->setCellValue("D{$row}", $details['punch_in_time']);
                    $sheet->setCellValue("E{$row}", $details['punch_out_time']);
                    $row++;
                }

                $filePath = storage_path("app/public/attendance-{$employeeId}.xlsx");
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save($filePath);

                if (!file_exists($filePath)) {
                    throw new \Exception('File not saved.');
                }
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to generate Excel file: ' . $e->getMessage()], 500);
            }
        }

        $fileUrl = asset("storage/" . basename($filePath));

        return response()->json(['file_url' => $fileUrl]);
    }

    public function generateAttendanceSheet(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $format = $request->input('format');

        $employees = Employee::all();
        $attendanceData = [];
        $startDate = Carbon::create($year, $month, 15)->subMonth();
        $endDate = Carbon::create($year, $month, 14);

        foreach ($employees as $employee) {
            $attendances = Attendance::where('emp_code', $employee->emp_code)
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->get()
                ->keyBy('date');

            $detailedAttendance = [];
            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                $currentDate = $date->toDateString();
                $attendance = $attendances->get($currentDate);

                $detailedAttendance[$currentDate] = [
                    'status' => $attendance->status ?? '--',
                ];
            }

            $attendanceData[] = [
                'employee_name' => $employee->name,
                'designation' => $employee->designation,
                'attendance' => $detailedAttendance,
            ];
        }

        // Generate the file
        if ($format === 'excel') {
            return $this->generateExcel($attendanceData, $month, $year);
        } elseif ($format === 'pdf') {
            return $this->generatePDF($attendanceData, $month, $year);
        } else {
            return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    private function generateExcel($attendanceData, $month, $year)
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->getDefaultColumnDimension()->setWidth(12);
            $sheet->getDefaultRowDimension()->setRowHeight(20);
            $sheet->getStyle('A1:Z100')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A1:Z100')->getAlignment()->setVertical('center');

            $sheet->setCellValue('A1', 'Employee Name');
            $sheet->setCellValue('B1', 'Designation');
            $columnIndex = 'C';

            $startDate = Carbon::create($year, $month, 15)->subMonth();
            $endDate = Carbon::create($year, $month, 14);

            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                $sheet->setCellValue("{$columnIndex}1", $date->format('D'));
                $sheet->setCellValue("{$columnIndex}2", $date->format('M d'));
                $sheet->getColumnDimension($columnIndex)->setWidth(6);
                $columnIndex++;
            }

            // Add total calculation headers
            $sheet->setCellValue("{$columnIndex}1", 'MTD Week Off Days');
            $sheet->setCellValue("{$columnIndex}2", '(WO)');
            $weekOffColumn = $columnIndex++;

            $sheet->setCellValue("{$columnIndex}1", 'MTD Present Days');
            $sheet->setCellValue("{$columnIndex}2", '(P)');
            $presentColumn = $columnIndex++;

            $sheet->setCellValue("{$columnIndex}1", 'MTD Absent Days');
            $sheet->setCellValue("{$columnIndex}2", '(A)');
            $absentColumn = $columnIndex++;

            $sheet->setCellValue("{$columnIndex}1", 'MTD Half Day');
            $sheet->setCellValue("{$columnIndex}2", '(H)');
            $halfDayColumn = $columnIndex++;

            $sheet->setCellValue("{$columnIndex}1", 'Total Present Days');
            $sheet->mergeCells("{$columnIndex}1:{$columnIndex}2");
            $totalPresentColumn = $columnIndex++;


            $sheet->mergeCells('A1:A2');
            $sheet->mergeCells('B1:B2');
            $sheet->getStyle('A1:AL2')->getFont()->setBold(true);
            $sheet->getStyle('A1:AL2')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('891010');
            $sheet->getStyle('A1:AL2')->getFont()->getColor()->setARGB('FFFFFF');
            $rowIndex = 3;
            foreach ($attendanceData as $data) {
                $sheet->setCellValue("A{$rowIndex}", $data['employee_name']);
                $sheet->setCellValue("B{$rowIndex}", $data['designation']);

                $columnIndex = 'C';
                foreach ($data['attendance'] as $date => $details) {
                    $shortStatus = match (strtolower($details['status'])) {
                        'present' => 'P',
                        'absent' => 'A',
                        'halfday' => 'H',
                        'weekend' => 'WO',
                        'holiday' => 'HO',
                        default => '--',
                    };

                    $sheet->setCellValue("{$columnIndex}{$rowIndex}", $shortStatus);

                    $color = match ($shortStatus) {
                        'P' => '90EE90',
                        'A' => 'FF6347',
                        'H' => 'FFFF99',
                        'WO' => '87CEEB',
                        'HO' => 'ADD8E6',
                        default => 'FFFFFF',
                    };
                    $sheet->getStyle("{$columnIndex}{$rowIndex}")->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($color);

                    $columnIndex++;
                }
                $sheet->setCellValue("{$weekOffColumn}{$rowIndex}", 0);
                $sheet->setCellValue("{$presentColumn}{$rowIndex}", 0);
                $sheet->setCellValue("{$absentColumn}{$rowIndex}", 0);
                $sheet->setCellValue("{$halfDayColumn}{$rowIndex}", 0);
                $sheet->setCellValue("{$totalPresentColumn}{$rowIndex}", 0 + (0 / 2));
                $rowIndex++;
            }
            $rowIndex--;
            $sheet->getStyle("A1:AL{$rowIndex}")
                ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $fileName = "attendance-sheet-{$year}-{$month}.xlsx";
            $filePath = storage_path("app/public/{$fileName}");
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($filePath);

            if (!file_exists($filePath)) {
                throw new \Exception('Excel file not saved.');
            }
            $fileUrl = asset("storage/{$fileName}");
            return response()->json(['file_url' => $fileUrl], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate Excel file: ' . $e->getMessage()], 500);
        }
    }

    private function generatePDF($attendanceData, $month, $year)
    {
        try {
            $pdf = Pdf::loadView('admin.pdf.attendance-sheet', [
                'attendanceData' => $attendanceData,
                'month' => $month,
                'year' => $year,
            ]);
            $fileName = "attendance-sheet-{$year}-{$month}.pdf";
            $filePath = storage_path("app/public/{$fileName}");
            $pdf->save($filePath);
            $fileUrl = asset("storage/{$fileName}");
            return response()->json(['file_url' => $fileUrl], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate PDF file: ' . $e->getMessage()], 500);
        }
    }
}
