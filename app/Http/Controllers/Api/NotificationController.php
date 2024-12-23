<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        // Fetch all notifications, ordered by creation date (latest first)
        $notifications = Notification::orderBy('created_at', 'desc')->get();

        return response()->json([
            'notifications' => $notifications, // Fixed key name for consistency
        ]);
    }
}
