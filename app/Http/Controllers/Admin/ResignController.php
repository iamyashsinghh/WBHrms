<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ResignAccept;
use App\Models\Employee;
use App\Models\Resignation;
use App\Services\HrMail;
use App\Services\SendFCMNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ResignController extends Controller
{
    public function index()
    {
        $page_heading = 'Resignation';
        return view('admin.resignation.list', compact('page_heading'));
    }

    public function ajax_list(Request $request)
    {
        $resignation = Resignation::with('employee:emp_code,name');
        return DataTables::of($resignation)->make(true);
    }

    public function approve(Request $request)
    {
        $auth_user = Auth::guard('admin')->user();
        $resignation = Resignation::find($request->id);
        if ($resignation) {
            $resignation->notice_period = $request->notice_period ?? 0;
            $resignation->accepted_at = now();
            $resignation->accepted_by = $auth_user->emp_code; 
            if ($resignation->save()) {
                $emp = Employee::where('emp_code', $resignation->emp_code)->first();
                HrMail::to($emp->email)->send(new ResignAccept($resignation->emp_code));
                if ($emp->notification_token) {
                    $last_working_day = Carbon::parse($resignation->resign_at)
                        ->addDays($resignation->notice_period)
                        ->format('d/m/Y');
                    $title = "Your Resignation is Accepted";
                    $body = " Dear $emp->name, This is to inform you that your resignation is accepted and the last working day will be marked as $last_working_day";
                    SendFCMNotification::to($emp->notification_token, $title, $body, [], 'https://cms.wbcrm.in/favicon.jpg');
                }
            }
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Accepted.']);
        }
        session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
        return redirect()->back();
    }
}
