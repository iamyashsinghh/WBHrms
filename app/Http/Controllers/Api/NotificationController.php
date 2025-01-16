<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = Notification::orderBy('created_at', 'desc')->get();
        $attendance = Attendance::where('date', Carbon::today()->toDateString())->where('emp_code', $user->emp_code)->whereNotNull('punch_in_time')->whereNull('punch_out_time')->first();
        return response()->json([
            'notifications' => $notifications,
            'attendance' => $attendance,
        ]);
    }


    public function setNotificationToken(Request $request)
    {
        $validated = $request->validate([
            'notification_token' => 'required|string',
        ]);
        $user = $request->user();
        $employee = Employee::where('emp_code', $user->emp_code)->first();
        if (!$employee) {
            return response()->json([
                'error' => 'Employee not found.',
            ], 404);
        }
        $employee->notification_token = $validated['notification_token'];
        $employee->save();
        return response()->json([
            'message' => 'Notification token updated successfully.',
        ], 200);
    }
}
