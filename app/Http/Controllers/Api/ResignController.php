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
}
