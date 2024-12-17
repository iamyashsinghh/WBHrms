<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\StoreLocation;
use Illuminate\Support\Facades\Log;
class LocationController extends Controller
{
    public function store_location(Request $request)
    {
        $u = $request->user();
        $user = Employee::where('emp_code', $u->emp_code)->first();
        Log::info('store_location');

        if (!$user) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'recorded_at' => 'required|date_format:Y-m-d H:i:s',
        ]);

        $location = StoreLocation::create([
            'emp_code' => $user->emp_code,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'recorded_at' => $validated['recorded_at'],
        ]);

        return response()->json([
            'message' => 'Location stored successfully',
            'location' => $location,
        ], 201);
    }
}
