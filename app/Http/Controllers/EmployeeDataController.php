<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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

public function update_profile_image(Request $request, $emp_code) {
    // Validate the input file
    $validate = Validator::make($request->all(), [
        'profile_image' => 'required|mimes:jpg,jpeg,png,webp,gif|max:1024',
    ]);

    if ($validate->fails()) {
        session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
        return redirect()->back();
    }

    // Find the employee
    $user = Employee::where('emp_code', $emp_code)->first();
    if (!$user) {
        abort(404, 'Employee not found.');
    }

    // Check and process the uploaded file
    if ($request->hasFile('profile_image') && $request->file('profile_image')->isValid()) {
        $file = $request->file('profile_image');
        $extension = $file->getClientOriginalExtension();

        $sub_str = substr($user->name, 0, 5);
        $file_name = strtolower(str_replace(' ', '_', $sub_str)) . "_profile_" . date('dmyHis') . "." . $extension;
        $path = "usersProfileImages/$file_name";

        try {
            $stored = Storage::disk('public')->putFileAs('usersProfileImages', $file, $file_name);
            Log::info($stored);
            if ($stored) {
                $profile_image_url = asset("storage/usersProfileImages/$file_name");

                $user->profile_img = $profile_image_url;
                $user->save();

                session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Image updated successfully.']);
            } else {
                session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Failed to store the image.']);
            }
        } catch (\Exception $e) {
            Log::error('Error saving profile image: ' . $e->getMessage());
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'An error occurred while saving the image.']);
        }
    } else {
        session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Invalid or no image file uploaded.']);
    }

    return redirect()->back();
}

}
