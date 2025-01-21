<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Salary;
use App\Models\SalarySlip;
use App\Models\SalaryType;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class PayRollController extends Controller
{
    public function index()
    {
        $page_heading = "Payroll";
        $users = Employee::where('is_active', 1)->get();
        return view('admin.payroll.list', compact('page_heading', 'users'));
    }

    public function ajax_list(Request $request)
    {
        $approval = SalarySlip::with('employee:emp_code,name');
        return DataTables::of($approval)->make(true);
    }

    public function update_is_paid($id)
    {
        $salary_slip = SalarySlip::findorfail($id);
        $salary_slip->is_paid = '1';
        $salary_slip->save();
        return response()->json([
            'success' => true,
            'message' => 'Status Updated.',
        ]);
    }

    public function destroy($id)
    {
        $salary_slip = SalarySlip::findorfail($id);
        $salary_slip->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted.',
        ]);
    }

    public function generateSalarySlip(Request $request)
    {
        $emp_code = $request->input('employee');
        $month = $request->input('month');
        $year = $request->input('year');
        if ($emp_code === 'all'){
            $users = Employee::where('is_active', 1)->get();
            foreach ($users as $user){
                $this->generatePDF($user->emp_code, $month, $year);
            }
        }else{
            $this->generatePDF($emp_code, $month, $year);
        }
        return response()->json([
            'success' => true,
            'message' => 'Salary slip generated and saved successfully.',
        ]);
    }

    private function generate($emp_code, $month = null, $year = null)
    {
        $employee = Employee::where('emp_code', $emp_code)->first();
        $salmonth = $month ?? now()->month;
        $salyear = $year ?? now()->year;
        if ($employee->emp_type === 'Fulltime') {
            $startDate = Carbon::create($salyear, $salmonth, 15)->subMonth();
            $endDate = Carbon::create($salyear, $salmonth, 14);
        } else {
            $startDate = Carbon::create($salyear, $salmonth, 1);
            $endDate = Carbon::create($salyear, $salmonth)->endOfMonth();
        }
        $attendances = Attendance::where('emp_code', $employee->emp_code)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])->get();
        $total_days_this_month = $startDate->diffInDays($endDate) + 1;
        $total_days = 30;
        $present = $attendances->whereIn('status', ['present', 'shortleave'])->count();
        $halfday = $attendances->where('status', 'halfday')->count();
        $absent = $attendances->where('status', 'absent')->count();
        $wo = $attendances->where('status', 'wo')->count();
        $holiday = $attendances->where('status', 'holiday')->count();
        $cl = $attendances->where('status', 'cl')->count();
        $pl = $attendances->where('status', 'pl')->count();
        $unmarked = $total_days_this_month - $attendances->count();
        $total_present = $total_days - $halfday / 2 - $absent - $unmarked;
        if($total_present < 0){
            $total_present = 0;
        }
        $absent = $unmarked + $absent;
        return [
            'total_days' => $total_days,
            'present' => $present,
            'absent' => $absent,
            'halfday' => $halfday,
            'wo' => $wo,
            'holiday' => $holiday,
            'cl' => $cl,
            'pl' => $pl,
            'unmarked' => $unmarked,
            'total_days_this_month' => $total_days_this_month,
            'total_present' => $total_present,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
    }

    private function generatePDF($emp_code = null, $month = null, $year = null)
    {
        $auth_user = Auth::user();
        $employee = Employee::where('emp_code', $emp_code)->first();
        $data = $this->generate($employee->emp_code, $month, $year);
        $salaryTypes = SalaryType::all();
        $salaries = [];
        $total_salary = 0;
        foreach ($salaryTypes as $type) {
            $salary = Salary::where('emp_code', $emp_code)
                ->where('salary_type', $type->id)
                ->first();
            $salaries[$type->name] = $salary ? $salary->salary : 0;
            $total_salary = $total_salary + $salary->salary;
        }
        $per_day_salary = $total_salary / $data['total_days'];
        $total_present = $data['total_present'];
        $salary_to_be_paid = $total_present * $per_day_salary;
        $paying_salaries = [];
        foreach ($salaryTypes as $type) {
            $paying_salaries[$type->name] = $salary_to_be_paid * ($type->value / 100);
        }
        $compact_data = compact(
            'data',
            'employee',
            'salaryTypes',
            'salaries',
            'total_salary',
            'per_day_salary',
            'salary_to_be_paid',
            'paying_salaries',
        );
        $pdf = PDF::loadView('pdf.salary_slip', $compact_data);
        $timestamp = now()->format('YmdHis');
        $fileName = "salary_slip_{$emp_code}_{$timestamp}.pdf";

        $filePath = storage_path("app/public/salaryslip/{$fileName}");
        $pdf->save($filePath);
        $fileUrl = asset("storage/public/salaryslip/" . basename($filePath));

        $salary_slip = SalarySlip::firstOrNew(
            ['emp_code' => $emp_code, 'month' => $month, 'year' => $year],
            ['created_by' => $auth_user->emp_code]
        );
        $salary_slip->is_paid = '0';
        $salary_slip->path = $fileUrl;
        $salary_slip->save();

    }
}
