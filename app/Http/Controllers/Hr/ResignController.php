<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Resignation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ResignController extends Controller
{
    public function index()
    {
        $page_heading = 'Resignation';
        return view('hr.resignation.list', compact('page_heading'));
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
            $resignation->save();

            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Accepted.']);
        }
        session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
        return redirect()->back();
    }
}
