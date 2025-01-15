<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
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
}
