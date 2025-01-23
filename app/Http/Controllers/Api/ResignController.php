<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resignation;
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
        $resignations = Resignation::with('employee')
            ->orderBy('created_at', 'desc')
            ->get();

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

        $resignation->status = 'approved';
        $resignation->notice_period = $request->notice_period ?? 0;
        $resignation->accepted_at = now();
        $resignation->accepted_by = $auth_user->emp_code;
        $resignation->save();

        return response()->json([
            'success' => true,
            'message' => 'Resignation approved successfully.',
            'data' => $resignation,
        ]);
    }

}
