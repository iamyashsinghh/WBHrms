<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Salary;
use App\Models\SalaryType;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    public function list()
    {
        $page_heading = 'Employees';
        $roles = Role::all();
        return view('admin.employee.list', compact('page_heading', 'roles'));
    }

    public function ajax_list(Request $request)
    {
        $role_id = $request->input('role_id');
        $users = Employee::select(
            'employees.id',
            'employees.emp_code',
            'employees.name',
            'employees.phone',
            'employees.is_active',
            'employees.status',
            'employees.profile_img',
            'employees.employee_designation',
            'role.name as role_name',
        )->leftJoin("roles as role", 'employees.role_id', '=', 'role.id');
        if ($role_id) {
            $users->where('employees.role_id', $role_id);
        }
        return DataTables::of($users->get())->make(true);
    }

    public function manage($emp_code = 0)
    {
        $roles = Role::all();
        $page_heading = '';
        $employees = Employee::select('emp_code', 'name')->get();
        if ($emp_code == 0) {
            $page_heading = 'Create New Employee';
            $user = json_decode(json_encode([
                "emp_code" => "0",
                "name" => "",
                "email" => "",
                "phone" => "",
                "alt_phone" => "",
                "role_id" => null,
                "dob" => null,
                "gender" => null,
                "marital_status" => null,
                "nationality" => null,
                "emp_type" => null,
                "doj" => null,
                "employee_designation" => null,
                "department" => null,
                "blood_group" => null,
                "profile_img" => null,
                "office_number" => null,
                "office_email" => null,
                "office_email_password" => null,
                "office_email_recovery_info" => null,
                "permanent_address" => null,
                "current_address" => null,
                "reporting_manager" => null,
                "e_phone" => null,
                "e_name" => null,
                "e_relation" => null,
                "e_address" => null,
                "medical_condition" => null,
                "bank_name" => null,
                "branch_name" => null,
                "account_number" => null,
                "ifsc_code" => null,
                "holder_name" => null,
                "all_info_filled" => null,
                "is_active" => 1,
                "status" => null,
            ]));
        } else {
            $user = Employee::where('emp_code', $emp_code)->first();
            $page_heading = "Edit $user->name's Details";
        }
        if (!$user) {
            return abort(404);
        }
        return view('admin.employee.manage', compact('user', 'roles', 'page_heading', 'employees'));
    }

    public function manage_process(Request $request, $emp_code = 0)
    {
        $validatedData = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|digits:10',
            'email' => 'nullable|email|max:255',
            'gender' => 'required|in:male,female,others',
            'employee_designation' => 'required|string|max:255',
            'dob' => 'required|date',
            'doj' => 'required|date',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'emp_type' => 'required|in:Fulltime,Intern',
            'nationality' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'blood_group' => 'nullable|string|max:3',
            'reporting_manager' => 'required|exists:employees,emp_code',
            'alt_phone' => 'nullable|digits:10',
            'permanent_address' => 'nullable|string|max:1000',
            'current_address' => 'nullable|string|max:1000',
            'office_number' => 'nullable|string|max:15',
            'office_email' => 'nullable|email|max:255',
            'office_email_password' => 'nullable|string|max:255',
            'office_email_recovery_info' => 'nullable|string|max:1000',
            'e_name' => 'nullable|string|max:255',
            'e_phone' => 'nullable|digits:10',
            'e_relation' => 'nullable|string|max:255',
            'e_address' => 'nullable|string|max:1000',
            'medical_condition' => 'nullable|string|max:1000',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:20',
            'ifsc_code' => 'nullable|string|max:11',
            'holder_name' => 'nullable|string|max:255',
        ]);

        if ($emp_code == 0) {
            $rolePrefixes = [
                1 => 'A-',
                2 => 'H-',
                3 => 'B-',
                4 => 'B-',
                5 => 'F-',
            ];
            $prefix = $rolePrefixes[$validatedData['role_id']] ?? 'X-';
            $startingReference = 2021;
            $count = Employee::withTrashed()->count();
            $referenceNumber = $startingReference + $count;
            $validatedData['emp_code'] = $prefix . $referenceNumber;
            Employee::create($validatedData);
            return redirect()->route('admin.employee.list')->with('success', 'Employee created successfully!');
        } else {
            $employee = Employee::where('emp_code', $emp_code)->first();
            if (!$employee) {
                return abort(404);
            }
            $employee->update($validatedData);
            return redirect()->route('admin.employee.list')->with('success', 'Employee updated successfully!');
        }
    }

    public function view($emp_code)
    {
        $user = Employee::where('emp_code', $emp_code)->first();
        if (!$user) {
            return abort(404);
        }
        $page_heading = "$user->name's Details";
        $doc_type = DocumentType::get();
        $salary_type = SalaryType::get();
        $managers = Employee::select('emp_code', 'name')->get();
        $ctc = Salary::where('emp_code', $user->emp_code)->sum('salary');
        $total_ctc = $ctc*12;
        return view('admin.employee.view', compact('user', 'page_heading', 'doc_type', 'salary_type', 'ctc', 'total_ctc', 'managers'));
    }
}
