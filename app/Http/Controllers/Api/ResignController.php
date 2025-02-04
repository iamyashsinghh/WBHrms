<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ResignAccept;
use App\Models\Employee;
use App\Models\Resignation;
use App\Services\HrMail;
use App\Services\SendFCMNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResignController extends Controller
{
    public function getUserResignation(Request $request)
    {
        $user = $request->user();
        $resignation = Resignation::where('emp_code', $user->emp_code)->first();
        if ($resignation) {
            return response()->json([
                'status' => 'exists',
                'resignation' => $resignation,
            ]);
        }
        return response()->json([
            'status' => 'not_found',
        ]);
    }

    public function save(Request $request)
    {
        $emp = $request->user();
        $existingResignation = Resignation::where('emp_code', $emp->emp_code)->first();
        if ($existingResignation) {
            return response()->json([
                'status' => 'exists',
                'resignation' => $existingResignation
            ]);
        }
        $resignation = new Resignation();
        $resignation->emp_code = $emp->emp_code;
        $resignation->type = $request->type;
        $resignation->detail = $request->detail;
        $resignation->resign_at = $request->resign_at;
        $resignation->save();
        return response()->json([
            'success' => true,
            'status' => 'saved',
            'resignation' => $resignation
        ]);
    }

    public function index(Request $request)
    {
        $auth_user = $request->user();
        if ($auth_user->role_id == 6) {
            $resignations = Resignation::with('employee')
                ->whereHas('employee', function ($query) use ($auth_user) {
                    $query->where('reporting_manager', $auth_user->emp_code);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $resignations = Resignation::with('employee')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        return response()->json([
            'success' => true,
            'data' => $resignations,
        ]);
    }

    public function approve(Request $request, $id)
    {
        $resignation = Resignation::find($id);
        $auth_user = $request->user();
        if (!$resignation) {
            return response()->json(['success' => false, 'message' => 'Resignation not found.'], 404);
        }

        $resignation->notice_period = $request->notice_period ?? 0;
        $resignation->accepted_at = now();
        $resignation->accepted_by = $auth_user->emp_code;
        if ($resignation->save()) {
            $emp = Employee::where('emp_code', $resignation->emp_code)->first();
            HrMail::to($emp->email)->send(new ResignAccept($resignation->emp_code));
            if ($emp->notification_token) {
                $last_working_day = Carbon::parse($resignation->resign_at)
                    ->addDays((int) $resignation->notice_period)
                    ->format('d/m/Y');
                $title = "Your Resignation is Accepted";
                $body = " Dear $emp->name, This is to in form you that your resignation is accepted and the last working day will be marked as $last_working_day";
                SendFCMNotification::to($emp->notification_token, $title, $body, [], 'https://cms.wbcrm.in/favicon.jpg');
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Resignation approved successfully.',
            'data' => $resignation,
        ]);
    }
}
