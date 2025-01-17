<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaveManegment extends Controller
{
    public function leaveApprovalRequest(Request $request){
        $auth_user = $request->user();
        $approvals = Approval::select('approvals.*')->where('approvals.created_at', '>=', Carbon::now()->subDays(45))
        ->join('employees', 'approvals.emp_code', '=', 'employees.emp_code')
        ->where('employees.reporting_manager', $auth_user->emp_code)
        ->get();
        return response()->json(['approval' => $approvals]);
    }
}
