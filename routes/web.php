<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers;
use App\Mail\OfferLetter;
use App\Models\Employee;
use App\Models\Salary;
use App\Services\HrMail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ItMail;

Route::group(['middleware' => 'AuthCheck'], function () {
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::post('/login/verify', [AuthController::class, 'login_verify'])->name('login.verify');
    Route::post('/login/process', [AuthController::class, 'login_process'])->name('login.process');
    Route::post('/get_otp_for_wahtsapp_automated_login', [AuthController::class, 'get_otp_for_wahtsapp_automated_login'])->name('autologinsystem');
});
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('mail/{emp_code?}/{type?}', function ($emp_code, $type) {
    $emp = Employee::where('emp_code', $emp_code)->first();
    HrMail::to($emp->email)->send(new OfferLetter($emp_code));
})->name('send.hr.mail');


Route::middleware('verify_token')->group(function () {
    Route::middleware(['CheckLoginTime', 'CheckDevice'])->group(function () {
        /*
        |--------------------------------------------------------------------------
        | For Admin Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('/admin')->middleware('admin')->name('admin.')->group(function () {
            /*
            |--------------------------------------------------------------------------
            | Admin Global Routes
            |--------------------------------------------------------------------------
            */
            Route::get('/dashboard', [Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
            Route::get('update_profile_image', [Controllers\Admin\EmployeeController::class, 'update_profile_image'])->name('employee.update_profile_image');
            Route::get('bypass_login/{emp_code?}', [Controllers\Admin\EmployeeController::class, 'bypass_login'])->name('employee.bypass_login');

            /*
            |--------------------------------------------------------------------------
            | Admin Employee Routes
            |--------------------------------------------------------------------------
            */
            Route::prefix('/employee')->name('employee.')->group(function () {
                Route::get('list', [Controllers\Admin\EmployeeController::class, 'list'])->name('list');
                Route::get('ajax_list', [Controllers\Admin\EmployeeController::class, 'ajax_list'])->name('ajax_list');
                Route::get('view/{emp_code?}', [Controllers\Admin\EmployeeController::class, 'view'])->name('view');
                Route::get('manage/{emp_code?}', [Controllers\Admin\EmployeeController::class, 'manage'])->name('manage');
                Route::post('manage_process/{emp_code?}', [Controllers\Admin\EmployeeController::class, 'manage_process'])->name('manage_process');
                Route::get('delete/{emp_code?}', [Controllers\Admin\EmployeeController::class, 'delete'])->name('delete');

                Route::post('updateEmploymentInfo', [Controllers\EmployeeDataController::class, 'updateEmploymentInfo'])->name('updateEmploymentInfo');
            });

            /*
            |--------------------------------------------------------------------------
            | Admin Roles Routes
            |--------------------------------------------------------------------------
            */
            Route::prefix('/role')->name('role.')->group(function () {
                Route::get('/roles', [Controllers\Admin\RoleController::class, 'list'])->name('list');
                Route::post('/roles/update-punch-time', [Controllers\Admin\RoleController::class, 'updatePunchTime'])->name('updatePunchTime');
                Route::post('/roles/update-login-time', [Controllers\Admin\RoleController::class, 'updateLoginTime'])->name('updateLoginTime');
                Route::post('/roles/update-grace-time', [Controllers\Admin\RoleController::class, 'updateGraceTime'])->name('updateGraceTime');
                Route::post('/roles/update-lating-time', [Controllers\Admin\RoleController::class, 'updateLatingTime'])->name('updateLatingTime');
                Route::get('/roles/update-is-all-time-login/{role_id?}/{value?}', [Controllers\Admin\RoleController::class, 'updateIsAllTimeLogin'])->name('update.isAllTimeLogin');
            });

            /*
            |--------------------------------------------------------------------------
            | Admin Salary Type Routes
            |--------------------------------------------------------------------------
            */
            Route::prefix('/salary-type')->name('salary-type.')->group(function () {
                Route::get('list', [Controllers\Admin\SalaryController::class, 'list'])->name('list');
                Route::get('ajax_list', [Controllers\Admin\SalaryController::class, 'ajax_list'])->name('ajax_list');
                Route::post('manage_process/{id?}', [Controllers\Admin\SalaryController::class, 'manage_process'])->name('manage_process');
                Route::delete('delete/{id}', [Controllers\Admin\SalaryController::class, 'destroy'])->name('destroy');
            });


            /*
            |--------------------------------------------------------------------------
            | Admin Salary Routes
            |--------------------------------------------------------------------------
            */
            Route::prefix('/salary')->name('salary.')->group(function () {
                Route::post('save_all', [Controllers\Admin\SalaryController::class, 'saveSalary'])->name('save_all');
            });

            /*
            |--------------------------------------------------------------------------
            | Admin Document Type Routes
            |--------------------------------------------------------------------------
            */
            Route::prefix('/document-type')->name('document-type.')->group(function () {
                Route::get('list', [Controllers\Admin\DocumentController::class, 'list'])->name('list');
                Route::get('ajax_list', [Controllers\Admin\DocumentController::class, 'ajax_list'])->name('ajax_list');
                Route::post('manage_process/{id?}', [Controllers\Admin\DocumentController::class, 'manage_process'])->name('manage_process');
                Route::delete('delete/{id}', [Controllers\Admin\DocumentController::class, 'destroy'])->name('destroy');
            });

            /*
            |--------------------------------------------------------------------------
            | Admin Document Routes
            |--------------------------------------------------------------------------
            */
            Route::prefix('/document')->name('document.')->group(function () {
                Route::post('delete_doc/{id?}', [Controllers\Admin\DocumentController::class, 'destroy_doc'])->name('destroy');
                Route::post('upload_document', [Controllers\Admin\DocumentController::class, 'uploadDocument'])->name('upload');
            });

            /*
            |--------------------------------------------------------------------------
            | Admin Approval Routes
            |--------------------------------------------------------------------------
            */
            Route::prefix('/approval')->name('approval.')->group(function () {
                Route::get('list/{dashboard_filters?}', [Controllers\Admin\ApprovalController::class, 'index'])->name('list');
                Route::get('approval/ajax_list', [Controllers\Admin\ApprovalController::class, 'ajax_list'])->name('ajax_list');
                Route::get('approval/update/{id?}/{status?}', [Controllers\Admin\ApprovalController::class, 'update_status'])->name('update_status');
            });

            /*
            |--------------------------------------------------------------------------
            | Admin ENV CONTROLLER
            |--------------------------------------------------------------------------
            */
            Route::prefix('/env')->name('env.')->group(function () {
                Route::get('/env-update', [Controllers\Admin\EnvController::class, 'index'])->name('index');
                Route::post('/env-update', [Controllers\Admin\EnvController::class, 'update'])->name('update');
            });
        });

        /*
        |--------------------------------------------------------------------------
        | For HR Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('/hr')->middleware('hr')->name('hr.')->group(function () {
            /*
            |--------------------------------------------------------------------------
            | HR Global Routes
            |--------------------------------------------------------------------------
            */
            Route::get('/dashboard', [Controllers\Hr\DashboardController::class, 'index'])->name('dashboard');
            Route::get('update_profile_image', [Controllers\Hr\EmployeeController::class, 'update_profile_image'])->name('employee.update_profile_image');
            Route::get('bypass_login/{emp_code?}', [Controllers\Hr\EmployeeController::class, 'bypass_login'])->name('employee.bypass_login');

            /*
            |--------------------------------------------------------------------------
            | Hr Employee Routes
            |--------------------------------------------------------------------------
            */
            Route::prefix('/employee')->name('employee.')->group(function () {
                Route::get('list', [Controllers\Hr\EmployeeController::class, 'list'])->name('list');
                Route::get('ajax_list', [Controllers\Hr\EmployeeController::class, 'ajax_list'])->name('ajax_list');
                Route::get('view/{emp_code?}', [Controllers\Hr\EmployeeController::class, 'view'])->name('view');
                Route::get('manage/{emp_code?}', [Controllers\Hr\EmployeeController::class, 'manage'])->name('manage');
                Route::post('manage_process/{emp_code?}', [Controllers\Hr\EmployeeController::class, 'manage_process'])->name('manage_process');
                Route::get('delete/{emp_code?}', [Controllers\Hr\EmployeeController::class, 'delete'])->name('delete');

                Route::post('updateEmploymentInfo', [Controllers\EmployeeDataController::class, 'updateEmploymentInfo'])->name('updateEmploymentInfo');
            });

            /*
            |--------------------------------------------------------------------------
            | Hr Salary Type Routes
            |--------------------------------------------------------------------------
            */
            Route::prefix('/salary-type')->name('salary-type.')->group(function () {
                Route::get('list', [Controllers\Hr\SalaryController::class, 'list'])->name('list');
                Route::get('ajax_list', [Controllers\Hr\SalaryController::class, 'ajax_list'])->name('ajax_list');
                Route::post('manage_process/{id?}', [Controllers\Hr\SalaryController::class, 'manage_process'])->name('manage_process');
                Route::delete('delete/{id}', [Controllers\Hr\SalaryController::class, 'destroy'])->name('destroy');
            });


            /*
            |--------------------------------------------------------------------------
            | Hr Salary Routes
            |--------------------------------------------------------------------------
            */
            Route::prefix('/salary')->name('salary.')->group(function () {
                Route::post('save_all', [Controllers\Hr\SalaryController::class, 'saveSalary'])->name('save_all');
            });

            /*
            |--------------------------------------------------------------------------
            | Hr Document Type Routes
            |--------------------------------------------------------------------------
            */
            Route::prefix('/document-type')->name('document-type.')->group(function () {
                Route::get('list', [Controllers\Hr\DocumentController::class, 'list'])->name('list');
                Route::get('ajax_list', [Controllers\Hr\DocumentController::class, 'ajax_list'])->name('ajax_list');
                Route::post('manage_process/{id?}', [Controllers\Hr\DocumentController::class, 'manage_process'])->name('manage_process');
                Route::delete('delete/{id}', [Controllers\Hr\DocumentController::class, 'destroy'])->name('destroy');
            });

            /*
            |--------------------------------------------------------------------------
            | Hr Document Routes
            |--------------------------------------------------------------------------
            */
            Route::prefix('/document')->name('document.')->group(function () {
                Route::post('delete_doc/{id?}', [Controllers\Hr\DocumentController::class, 'destroy_doc'])->name('destroy');
                Route::post('upload_document', [Controllers\Hr\DocumentController::class, 'uploadDocument'])->name('upload');
            });

            /*
            |--------------------------------------------------------------------------
            | Hr All Emp Attendance Routes
            |--------------------------------------------------------------------------
            */
            Route::prefix('attendance-all')->name('attendance.')->group(function () {
                Route::get('fetch-attendance', [Controllers\Hr\AttendanceController::class, 'fetchAttendance'])->name('fetch');
                Route::get('get/{emp_code?}/{date?}', [Controllers\Hr\AttendanceController::class, 'get_attendance'])->name('get');
                Route::post('store/{emp_code?}/{date?}', [Controllers\Hr\AttendanceController::class, 'store_attendance'])->name('store');
            });
        });
    });
});
