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
        $page_heading = "Live location";
        return view('admin.geo.live_location', [
            'page_heading' => $page_heading,
        ]);
    }

    public function get_all_emp_location_ajax()
    {
        $todayDate = Carbon::today()->toDateString();
        $latestLocations = StoreLocation::select(
            'store_locations.*',
            'employees.name as employee_name',
            'employees.profile_img',
            'attendances.status as attendance_status',
            'attendances.punch_in_time',
            'attendances.punch_out_time',
        )
            ->join('employees', 'employees.emp_code', '=', 'store_locations.emp_code')
            ->leftJoin('attendances', function ($join) use ($todayDate) {
                $join->on('store_locations.emp_code', '=', 'attendances.emp_code')
                    ->whereDate('attendances.date', '=', $todayDate);
            })
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

        $todayDate = Carbon::now()->toDateString();
        $query = StoreLocation::where('store_locations.emp_code', $user->emp_code)
            ->select(
                'store_locations.*',
                'employees.name as employee_name',
                'employees.profile_img',
                'attendances.status as attendance_status',
                'attendances.punch_in_time',
                'attendances.punch_out_time'
            )
            ->join('employees', 'store_locations.emp_code', '=', 'employees.emp_code')
            ->leftJoin('attendances', function ($join) use ($todayDate) {
                $join->on('store_locations.emp_code', '=', 'attendances.emp_code')
                    ->whereDate('attendances.date', '=', $todayDate);
            })
            ->joinSub(
                StoreLocation::selectRaw('emp_code, MAX(created_at) as latest_time')
                    ->groupBy('emp_code'),
                'latest_locations',
                function ($join) {
                    $join->on('store_locations.emp_code', '=', 'latest_locations.emp_code')
                        ->on('store_locations.created_at', '=', 'latest_locations.latest_time');
                }
            );

        if ($request->has('date')) {
            $query->whereDate('store_locations.recorded_at', $request->date);
        }

        $locations = $query->orderBy('store_locations.recorded_at')->get();


        if ($locations->isEmpty()) {
            return response()->json(['message' => 'No locations found for this employee'], 404);
        }

        return response()->json($locations);
    }
}
