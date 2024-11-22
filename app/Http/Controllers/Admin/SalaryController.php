<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use App\Models\SalaryType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SalaryController extends Controller
{
    public function list()
    {
        $page_heading = "Salary Types";
        return view('admin.salary.type.list', compact('page_heading'));
    }

    /**
     * Handle DataTable AJAX requests.
     */
    public function ajax_list(Request $request)
    {
        if ($request->ajax()) {
            $data = SalaryType::query();
            return DataTables::of($data)
                ->addColumn('actions', function ($row) {
                    return '
                        <button class="btn btn-sm btn-primary" onclick="handleManageSalaryType(' . $row->id . ')">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="handleDeleteSalaryType(' . $row->id . ')">
                            <i class="fa fa-trash"></i>
                        </button>';
                })
                ->rawColumns(['actions', 'icon'])
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
            'icon' => 'nullable|string|max:255',
        ]);

        $data = $request->only(['name', 'value', 'icon']);

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

        if($ctc != 0 && $new_ctc > $ctc){
            $increment = $new_ctc - $ctc;
            Log::info("$increment");
        }

        return response()->json([
            'success' => true,
            'message' => 'Salaries updated successfully.',
        ]);
    }

}
