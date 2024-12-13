<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApprovalController extends Controller
{
    public function newApproval(Request $request){
        $u = $request->user();
        $user = Employee::where('emp_code', $u->emp_code)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validate = Validator::make($request->all(), [
            'type' => "required|string",
            'start' => "required",
            'emp_desc' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'All feilds are required.'], 400);
        }
        

    }
}
