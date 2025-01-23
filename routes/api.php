<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;
use Illuminate\Http\Request;


Route::group(['middleware' => 'api.auth'], function () {
    Route::get('yash-auth', function () {
        return json_encode(['msg' => 'Hello Auth User']);
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

        // documents route
        Route::get('get-docs', [Api\DocumentController::class, 'getDocs']);
        Route::post('create-doc', [Api\DocumentController::class, 'createDocs']);

        // notification route
        Route::get('get-notification', [Api\NotificationController::class, 'index']);
        Route::post('save-notification-token', [Api\NotificationController::class, 'setNotificationToken']);

        // salary routes
        Route::get('get-salary-preview/{month?}/{year?}', [Api\SalaryPreviewController::class, 'get_salary']);

        // leave mgmt
        Route::get('leave-requests', [Api\LeaveManegment::class , 'leaveApprovalRequest']);
        Route::post('leave-requests-update-status/{id}/{status}', [Api\LeaveManegment::class , 'update_status']);
        Route::get('leave-get-users', [Api\LeaveManegment::class , 'getUsers']);
        Route::get('leave-get-user/{emp_code}', [Api\LeaveManegment::class , 'getUser']);
        Route::post('leave-new-approval/{emp_code}', [Api\LeaveManegment::class , 'leaveNewApproval']);

        // resignation mgmt
        Route::get('/user/resignation',  [Api\ResignController::class , 'getUserResignation']);
        Route::post('/user/applyresignation',  [Api\ResignController::class , 'save']);
        // resignation mgmt
        Route::get('/resignations', [Api\ResignController::class, 'index']);
        Route::post('/resignations/{id}/approve', [Api\ResignController::class, 'approve']);
    });
});


// test api
Route::get('yash', function(){
    return 'hello';
});

