<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function list() {
        $page_heading = "Roles & Permissions";
        $roles = Role::all();
        return view('admin.role.list', compact('page_heading', 'roles'));
    }

    public function updatePunchTime(Request $request)
    {
        $role = Role::find($request->role_id);

        if ($request->type == 'punchin') {
            $users = Employee::where('role_id', $role->id)
            ->get();
            $role->puch_in_time = $request->value;
            foreach ($users as $user){
                $user->puch_in_time = $request->value;
                $user->save();
            }
        } else if ($request->type == 'punchout') {
            $users = Employee::where('role_id', $role->id)
            ->get();
            $role->puch_out_time = $request->value;
            foreach ($users as $user){
                $user->puch_out_time = $request->value;
                $user->save();
            }
        }

        $role->save();

        return response()->json(['success' => true, 'message' => 'Punch time updated successfully.']);
    }

    public function updateLoginTime(Request $request)
    {
        $role = Role::find($request->role_id);

        if ($request->type == 'start') {
            $role->login_start_time = $request->value;
        } else if ($request->type == 'end') {
            $role->login_end_time = $request->value;
        }

        $role->save();

        return response()->json(['success' => true, 'message' => 'Login time updated successfully.']);
    }

    public function updateGraceTime(Request $request)
    {
        $role = Role::find($request->role_id);
        $role->grace_time = $request->value;
        $role->save();

        return response()->json(['success' => true, 'message' => 'Grace time updated successfully.']);
    }

    public function updateLatingTime(Request $request)
    {
        $role = Role::find($request->role_id);
        $role->lating_time = $request->value;
        $role->save();

        return response()->json(['success' => true, 'message' => 'Lating time updated successfully.']);
    }

    public function updateIsAllTimeLogin($role_id, $value) {
        $role = Role::find($role_id);
        if (!$role) {
            return abort(404);
        }

        $role->is_all_time_login = $value;
        $role->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Is all time login updated."]);
        return redirect()->back();
    }
}
