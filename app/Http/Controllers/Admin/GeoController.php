<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\StoreLocation;
use Illuminate\Http\Request;

class GeoController extends Controller
{
    public function index($emp_code)
    {
        $user = Employee::where('emp_code', $emp_code)->first();

        if (!$user) {
            abort(404);
        }

        $page_heading = "{$user->name} Live location";

        return view('admin.geo.employee_live_location', [
            'page_heading' => $page_heading,
            'user' => $user
        ]);
    }

    public function get_last_location_ajax($emp_code)
    {
        $user = Employee::where('emp_code', $emp_code)->first();
        if (!$user) {
            return response()->json(['message' => 'No employee found'], 404);
        }
        $location = StoreLocation::where('emp_code', $user->emp_code)->latest()->first();
        if (!$location) {
            return response()->json(['message' => 'No location found for this employee'], 404);
        }
        return response()->json($location);
    }
}
