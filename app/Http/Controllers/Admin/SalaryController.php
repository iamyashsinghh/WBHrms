<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\IncrementLetter;
use App\Models\Employee;
use App\Models\Salary;
use App\Models\SalaryType;
use App\Services\HrMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SalaryController extends Controller
{
    public function list()
    {
        $page_heading = "Salary Summary";

        $emp_code = 'A-2021';
        $salaryData = Salary::where('emp_code', $emp_code)
        ->with('salaryType')
        ->join('salary_types', 'salary.salary_type', '=', 'salary_types.id')
        ->orderBy('salary_types.id', 'asc')
        ->select('salary.*')
        ->get();
                $salarySummary = [];
        foreach ($salaryData as $salary) {
            $perMonth = $salary->salary;
            $perAnnum = $salary->salary * 12;
            $salarySummary[] = [
                'name' => $salary->salaryType->name,
                'category' => $salary->salaryType->category,
                'per_month' => $perMonth,
                'per_annum' => $perAnnum,
            ];
        }
        return view('admin.salary.type.list', compact('page_heading', 'salarySummary'));
    }

    /**
     * Handle DataTable AJAX requests.
     */
    public function ajax_list(Request $request)
    {
        if ($request->ajax()) {
            $data = SalaryType::query();
            return DataTables::of($data)
                ->make(true);
        }
    }

    /**
     * Handle the creation or update of salary types.
     */
    public function manage_process(Request $request, $id = null)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'required|numeric',
            'category' => 'nullable|string|max:255',
        ]);

        $data = $request->only(['name', 'value', 'category']);

        if ($id) {
            $salaryType = SalaryType::findOrFail($id);
            $salaryType->update($data);
            return response()->json(['success' => 'Salary type updated successfully.']);
        } else {
            SalaryType::create($data);
            return response()->json(['success' => 'Salary type created successfully.']);
        }
    }

    /**
     * Delete a salary type.
     */
    public function destroy($id)
    {
        $salaryType = SalaryType::findOrFail($id);
        Salary::where('salary_type', $salaryType->id)->delete();
        $salaryType->delete();

        return response()->json(['success' => 'Salary type deleted successfully.']);
    }

    public function saveSalary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'salaryData' => 'required|array',
            'salaryData.*.salary_type' => 'required|integer|exists:salary_types,id',
            'salaryData.*.emp_code' => 'required|string|exists:employees,emp_code',
            'salaryData.*.salary' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $salaryData = $request->input('salaryData');
        $emp_code = $salaryData[0]['emp_code'];
        $ctc = Salary::where('emp_code', $emp_code)->sum('salary');


        foreach ($salaryData as $data) {
            Salary::updateOrCreate(
                [
                    'emp_code' => $data['emp_code'],
                    'salary_type' => $data['salary_type'],
                ],
                [
                    'salary' => $data['salary'],
                ]
            );
        }

        $new_ctc = Salary::where('emp_code', $emp_code)->sum('salary');

        if ($ctc != 0 && $new_ctc > $ctc) {
            $increment = $new_ctc - $ctc;
            $data = Employee::where('emp_code', $emp_code)->first();
            $data->inc_amt = $increment;
            $data->new_salary = $new_ctc;
            HrMail::to($data->mail)->send(new IncrementLetter($data));
        }

        return response()->json([
            'success' => true,
            'message' => 'Salaries updated successfully.',
        ]);
    }
}
