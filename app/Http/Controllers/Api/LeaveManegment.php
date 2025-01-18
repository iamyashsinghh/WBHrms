<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;

class LeaveManegment extends Controller
{
    public function leaveApprovalRequest(Request $request){
        $auth_user = $request->user();
        $approvals = Approval::select('approvals.*', 'employees.name as emp_name')->where('approvals.created_at', '>=', Carbon::now()->subDays(45))
        ->join('employees', 'approvals.emp_code', '=', 'employees.emp_code')
        ->where('employees.reporting_manager', $auth_user->emp_code)
        ->get();
        return response()->json(['approval' => $approvals]);
    }

    public function update_status($id, $status)
    {
        $approval = Approval::findOrFail($id);
        $user = Employee::where('emp_code', $approval->emp_code)->first();

        if ($status == 2) {
            $approval->is_approved = $status;
            $approval->save();
            session()->flash('status', ['success' => true, 'alert_type' => 'error', 'message' => "Approval Rejected."]);
            return redirect()->back();
        } else if ($status == 1) {
            $approval->is_approved = $status;
            $approval->save();

            if ($approval->type == 'pl') {
                $start = new DateTime($approval->start);
                $end = ($approval->end) ? new DateTime($approval->end) : $start;

                $period = new DatePeriod(
                    $start,
                    new DateInterval('P1D'),
                    $end->modify('+1 day')
                );

                foreach ($period as $date) {
                    $dateStr = $date->format('Y-m-d');
                    $attendance = Attendance::firstOrNew(['date' => $dateStr, 'emp_code' => $approval->emp_code]);
                    $attendance->status = 'pl';
                    $user->pl_left--;
                    $attendance->save();
                }
            }
            if($approval->type == 'wo'){
                $start = new DateTime($approval->start);
                $end = ($approval->end) ? new DateTime($approval->end) : $start;

                $period = new DatePeriod(
                    $start,
                    new DateInterval('P1D'),
                    $end->modify('+1 day')
                );

                foreach ($period as $date) {
                    $dateStr = $date->format('Y-m-d');
                    $attendance = Attendance::firstOrNew(['date' => $dateStr, 'emp_code' => $approval->emp_code]);
                    $attendance->status = 'wo';
                    $attendance->save();
                }
            }
            if($approval->type == 'sl' || $approval->type == 'hd' || $approval->type == 'cl'){
                $attendance = Attendance::firstOrNew(['date' => $approval->start, 'emp_code' => $approval->emp_code]);
                switch ($approval->type) {
                    case 'sl':
                        $attendance->status = 'shortleave';
                        break;
                    case 'hd':
                        $attendance->status = 'halfday';
                        break;
                    case 'cl':
                        $attendance->status = 'cl';
                        $user->cl_left--;
                        break;
                }
                $attendance->save();
                $user->save();
            }
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Approved."]);
            return redirect()->back();
        }
    }
}
