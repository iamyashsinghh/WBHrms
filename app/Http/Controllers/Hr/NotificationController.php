<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class NotificationController extends Controller
{
    public function list(){
        $page_heading = 'Notifications';
        return view('admin.notification.list', compact('page_heading'));
    }

    public function ajax_list(Request $request)
    {
        if ($request->ajax()) {
            $data = Notification::query();
            return DataTables::of($data)
                ->make(true);
        }
    }

    public function manage_process(Request $request, $id = null)
    {
        $request->validate([
            'value' => 'required|string',
        ]);

        $data = $request->only(['value']);

        if ($id) {
            $notification = Notification::findOrFail($id);
            $notification->update($data);
            return response()->json(['success' => 'Updated successfully.']);
        } else {
            Notification::create($data);
            return response()->json(['success' => 'Created successfully.']);
        }
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return response()->json(['success' => 'Salary type deleted successfully.']);
    }

}
