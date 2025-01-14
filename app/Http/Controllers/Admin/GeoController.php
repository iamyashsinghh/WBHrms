<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\StoreLocation;
use Carbon\Carbon;
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


    public function index_all()
    {
        $employees_id_with_location = Attendance::whereNotNull('punch_in_time')
            ->whereNull('punch_out_time')
            ->whereDate('date', Carbon::now()->toDateString())
            ->pluck('emp_code');
        $employees_with_location = Employee::whereIn('emp_code', $employees_id_with_location)->get();

        $page_heading = "Live location";
        return view('admin.geo.live_location', [
            'page_heading' => $page_heading,
            'employees_with_location' => $employees_with_location,
            'employees_id_with_location' => $employees_id_with_location
        ]);
    }

    public function get_all_emp_location_ajax()
    {
        $latestLocations = StoreLocation::select('store_locations.*','employees.name as employee_name', 'employees.profile_img')
        ->join('employees', 'employees.emp_code', '=', 'store_locations.emp_code')
            ->joinSub(
                StoreLocation::selectRaw('emp_code, MAX(created_at) as latest_time')
                    ->groupBy('emp_code'),
                'latest_locations',
                function ($join) {
                    $join->on('store_locations.emp_code', '=', 'latest_locations.emp_code')
                        ->on('store_locations.created_at', '=', 'latest_locations.latest_time');
                }
            )
            ->get();
        if ($latestLocations->isEmpty()) {
            return response()->json(['message' => 'No locations found'], 404);
        }
        return response()->json($latestLocations);
    }

    public function index_history($emp_code)
    {
        $user = Employee::where('emp_code', $emp_code)->first();

        if (!$user) {
            abort(404);
        }

        $page_heading = "{$user->name} Live location";

        return view('admin.geo.employee_live_location_history', [
            'page_heading' => $page_heading,
            'user' => $user
        ]);
    }

    public function get_location_history_ajax(Request $request, $emp_code)
    {
        $user = Employee::where('emp_code', $emp_code)->first();
        if (!$user) {
            return response()->json(['message' => 'No employee found'], 404);
        }

        $query = StoreLocation::where('emp_code', $user->emp_code);

        if ($request->has('date')) {
            $query->whereDate('recorded_at', $request->date);
        }

        $locations = $query->orderBy('recorded_at')->get();

        if ($locations->isEmpty()) {
            return response()->json(['message' => 'No locations found for this employee'], 404);
        }

        return response()->json($locations);
    }
}
