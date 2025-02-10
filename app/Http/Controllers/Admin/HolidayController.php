<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class HolidayController extends Controller
{
    public function list()
    {
        $page_heading = "Holiday";
        return view('admin.holiday.list', compact('page_heading'));
    }

    public function get($id = null){
        $holiday = Holiday::findOrFail($id);
        return $holiday;
    }

    /**
     * Handle DataTable AJAX requests.
     */
    public function ajax_list(Request $request)
    {
        if ($request->ajax()) {
            $data = Holiday::query();
            return DataTables::of($data)
                ->addColumn('actions', function ($row) {
                    return '
                        <button class="btn btn-sm btn-primary" onclick="handleManageholiday(' . $row->id . ')">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="handleDeleteholiday(' . $row->id . ')">
                            <i class="fa fa-trash"></i>
                        </button>';
                })
                ->rawColumns(['actions', 'icon'])
                ->make(true);
        }
    }

    /**
     * Handle the creation or update of document types.
     */
    public function manage_process(Request $request, $id = null)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required',
        ]);

        $data = $request->only(['name', 'date']);

        if ($id) {
            $holiday = Holiday::findOrFail($id);
            $holiday->update($data);
            return response()->json(['success' => 'Document type updated successfully.']);
        } else {
            Holiday::create($data);
            return response()->json(['success' => 'Document type created successfully.']);
        }
    }

    /**
     * Delete a document type.
     */
    public function destroy($id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();
        return response()->json(['success' => 'Document type deleted successfully.']);
    }
}
