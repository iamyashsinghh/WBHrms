<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Approval;
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

        $approaval = new Approval();
        $approaval->emp_code = $user->emp_code;
        $approaval->type = $request->input('type');
        $approaval->start = $request->input('start');

        if ($request->input('type') == 'sl' || $request->input('type') == 'hd') {
            $time = $request->input('time');
            $emp_desc = "I will leave at $time";
            $emp_desc .= "\n" . $request->input('emp_desc');
            $approaval->emp_desc = $emp_desc;
        }elseif($request->input('type') == 'pl'){
            $approaval->end = $request->input('end');
            $emp_desc = $request->input('emp_desc');
            $emp_desc .= "\n  Frome" . $request->input('start') .' to '.  $request->input('end');
            $approaval->emp_desc = $emp_desc;
        }else{
            $approaval->emp_desc = $request->input('emp_desc');
        }

        if($approaval->save()){
            return response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Applied Successfully Please wait For Approval.'], 200);
        }else{
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Internal Server Error'], 500);
        }
    }
}
