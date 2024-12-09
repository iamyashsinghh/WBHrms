<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;
use Illuminate\Http\Request;


Route::group(['middleware' => 'api.auth'], function () {
    Route::get('test', function () {
        return json_encode(['msg' => 'Hi']);
    });
    Route::post('login_verify', [Api\AuthController::class, 'login_verify']);
    Route::post('login_process', [Api\AuthController::class, 'login_process']);

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('getuser', function(Request $request){
            return $request->user();
        });
        Route::get('attendance/{month}/{year}', [Api\AttendanceController::class, 'fetchUserAttendance']);
        Route::post('attendance/mark', [Api\AttendanceController::class, 'mark_attendance']);
        Route::get('/get-attendance/{day?}', [Api\AttendanceController::class, 'fetchDayAttendanceLog']);

        Route::post('/store-location', [Api\LocationController::class, 'store_location']);
    });
});

Route::get('yash', function(){
    return 'hello';
});
