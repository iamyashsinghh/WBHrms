<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index(){
        $notifcation = Notification::all();
        return response()->json([
            'notifcation' => $notifcation
        ]);
    }
}
