<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

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
            'custom_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
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

        $employeesData = $employeeQuery->get(['id', 'notification_token', 'profile_img']); // Include profile image in the query

        $imageUrl = null;

        if ($imageType === 'profile_image') {
        } elseif ($imageType === 'wedding_banquets_logo') {
            $imageUrl = 'path/to/wedding_banquets_logo.jpg';
        } elseif ($imageType === 'wedding_banquets_favicon') {
            $imageUrl = 'path/to/wedding_banquets_favicon.jpg';
        } elseif ($imageType === 'custom_image' && $customImage) {
            $imageUrl = $customImage->store('uploads/fcm_images', 'public');
        }

        foreach ($employeesData as $employee) {
            $finalImageUrl = $imageUrl;

            if ($imageType === 'profile_image') {
                $finalImageUrl = $employee->profile_img ? $employee->profile_img : null;
            }

            if ($employee->notification_token) {
                sendFCMNotification($employee->notification_token, $title, $body, [], $finalImageUrl);
            }
        }

        return redirect()->back()->with('success', 'Notifications sent successfully!');
    }
}
