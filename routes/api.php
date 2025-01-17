<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;
use Illuminate\Http\Request;


Route::group(['middleware' => 'api.auth'], function () {
    Route::get('test', function () {
        return json_encode(['msg' => 'Hi']);
    });

    // login routes
    Route::post('login_verify', [Api\AuthController::class, 'login_verify']);
    Route::post('login_process', [Api\AuthController::class, 'login_process']);

    Route::group(['middleware' => 'auth:sanctum'], function () {
        // get user route
        Route::get('getuser', function(Request $request){
            return $request->user();
        });

        // attendance route
        Route::get('attendance/{month}/{year}', [Api\AttendanceController::class, 'fetchUserAttendance']);
        Route::post('attendance/mark', [Api\AttendanceController::class, 'mark_attendance']);
        Route::get('/get-attendance/{day?}', [Api\AttendanceController::class, 'fetchDayAttendanceLog']);

        // location route
        Route::post('/store-location', [Api\LocationController::class, 'store_location']);

        // leave route
        Route::post('/new-approval', [Api\ApprovalController::class, 'newApproval']);
        Route::get('attendance-for-cl/{month}/{year}', [Api\ApprovalController::class, 'clApprovalDates']);
        Route::get('get-approvals', [Api\ApprovalController::class, 'getApprovals']);

        //documents route
        Route::get('get-docs', [Api\DocumentController::class, 'getDocs']);
        Route::post('create-doc', [Api\DocumentController::class, 'createDocs']);

        // notification route
        Route::get('get-notification', [Api\NotificationController::class, 'index']);
        Route::post('save-notification-token', [Api\NotificationController::class, 'setNotificationToken']);

        //salary routes
        Route::get('get-salary-preview/{month?}/{year?}', [Api\SalaryPreviewController::class, 'get_salary']);
    });
});

Route::get('yash', function(){
    return 'hello';
});
