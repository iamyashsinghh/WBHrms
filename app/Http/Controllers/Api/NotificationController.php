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
        $attendance = Attendance::where('date', Carbon::today()->toDateString())->where('emp_code', $user->emo_code)->first();
        return response()->json([
            'notifications' => $notifications,
            'attendance' => $attendance,
        ]);
    }
}
