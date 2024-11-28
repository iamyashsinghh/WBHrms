<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeDataController extends Controller
{
    public function updateEmploymentInfo(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'emp_code' => 'required|exists:employees,emp_code',
                'uan' => 'nullable|string',
                'pan_number' => 'nullable|string',
                'aadhaar_number' => 'nullable|string',
                'pf_number' => 'nullable|string',
                'pf_joining_date' => 'nullable|date',
                'pf_eligible' => 'nullable|boolean',
                'esi_eligible' => 'nullable|boolean',
                'esi_number' => 'nullable|string',
                'pt_eligible' => 'nullable|boolean',
                'lwf_eligible' => 'nullable|boolean',
                'eps_eligible' => 'nullable|boolean',
                'eps_joining_date' => 'nullable|date',
                'eps_exit_date' => 'nullable|date',
                'hps_eligible' => 'nullable|boolean',
                'phone' => 'nullable|string',
                'alt_phone' => 'nullable|string',
                'email' => 'nullable|email',
                'gender' => 'nullable|string',
                'dob' => 'nullable|date',
                'marital_status' => 'nullable|string',
                'nationality' => 'nullable|string',
                'blood_group' => 'nullable|string',
                'medical_condition' => 'nullable|string',
                'department' => 'nullable|string',
                'doj' => 'nullable|date',
                'reporting_manager' => 'nullable|string',
                'office_email' => 'nullable|email',
                'office_number' => 'nullable|string',
                'office_email_password' => 'nullable|string',
                'office_email_recovery_info' => 'nullable|string',
                'e_name' => 'nullable|string',
                'e_phone' => 'nullable|string',
                'e_relation' => 'nullable|string',
                'e_address' => 'nullable|string',
                'current_address' => 'nullable|string',
                'permanent_address' => 'nullable|string',
                'bank_name' => 'nullable|string',
                'branch_name' => 'nullable|string',
                'account_number' => 'nullable|string',
                'ifsc_code' => 'nullable|string',
                'holder_name' => 'nullable|string',
            ]);
            $employee = Employee::where('emp_code', $validatedData['emp_code'])->firstOrFail();
            $employee->update($validatedData);
            return response()->json(['success' => true, 'message' => 'Employment information updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update employment information.', 'error' => $e->getMessage()], 500);
        }
    }
}
