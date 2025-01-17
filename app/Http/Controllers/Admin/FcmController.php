<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Services\SendFCMNotification;
use Illuminate\Support\Facades\Log;

class FcmController extends Controller
{
    public function index()
    {
        $users = Employee::where('is_active', 1)->whereNotNull('notification_token')->get();
        return view('admin.fcm.manage', compact('users'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'employees' => 'required|array',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image_type' => 'required|string',
            'custom_image' => 'nullable',
        ]);
        $employees = $request->input('employees');
        $title = $request->input('title');
        $body = $request->input('body');
        $imageType = $request->input('image_type');
        $customImage = $request->file('custom_image');
        $tokens = [];
        $employeeQuery = in_array('all', $employees)
            ? Employee::where('is_active', 1)
            : Employee::whereIn('id', $employees);

        $employeesData = $employeeQuery->get(['id', 'notification_token', 'profile_img']);

        $imageUrl = null;

        if ($imageType === 'profile_image') {
        } elseif ($imageType === 'wedding_banquets_logo') {
            $imageUrl = 'https://cms.wbcrm.in/wb-logo2.webp';
        } elseif ($imageType === 'wedding_banquets_favicon') {
            $imageUrl = 'https://cms.wbcrm.in/favicon.jpg';
        } elseif ($imageType === 'custom_image' && $customImage) {
            $imagePath = $customImage->store('uploads/fcm_images', 'public');
            Log::info("$imagePath");
            $imageUrl = asset('storage/' . $imagePath);
            Log::info("$imageUrl");
        }
        foreach ($employeesData as $employee) {
            $finalImageUrl = $imageUrl;
            if ($imageType === 'profile_image') {
                $finalImageUrl = $employee->profile_img ? asset('storage/employee-profile/' . $employee->profile_img) : null;
            }
            Log::info($finalImageUrl);
            if ($employee->notification_token) {
                SendFCMNotification::to($employee->notification_token, $title, $body, [], $finalImageUrl);
            }
        }
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Notifications sent successfully!']);
        return redirect()->back()->with('success', 'Notifications sent successfully!');
    }
}
