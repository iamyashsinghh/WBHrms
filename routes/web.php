<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers;
use App\Mail\OfferLetter;
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

Route::get('mail', function(){
    $data = [
        'company_name' => 'wedding',
        'location' => 'wedding',
        'acceptance_deadline' => 'wedding',
        'contact_email' => 'wedding',
        'candidate_name' => 'John Doe',
        'position' => 'Software Engineer',
        'start_date' => '2024-12-01',
        'salary' => '$70,000',
    ];
    HrMail::to('iamyashsinghh@gmail.com')->send(new OfferLetter($data));
});
Route::get('mail_view', function () {
    $data = [
        'candidate_name' => 'Yash Singh',
        'position' => 'Software Engineer',
        'company_name' => 'Wedding Inc.',
        'start_date' => '2024-12-01',
        'salary' => '$70,000',
        'location' => 'New York',
        'acceptance_deadline' => '2024-12-15',
        'contact_email' => 'hr@weddinginc.com',
    ];

    $pdf = PDF::loadView('mail.offerletterpdf', compact('data'))
        ->setPaper('a4', 'portrait');

    return $pdf->download('OfferLetter.pdf');
});
Route::get('mail_vie', function () {
    $data = [
        'candidate_name' => 'Yash Singh',
        'position' => 'Software Engineer',
        'company_name' => 'Wedding Inc.',
        'start_date' => '2024-12-01',
        'salary' => '$70,000',
        'location' => 'New York',
        'acceptance_deadline' => '2024-12-15',
        'contact_email' => 'hr@weddinginc.com',
    ];



    return view('mail.offerletterpdf', ['data' => $data]);
});



Route::middleware('verify_token')->group(function () {
    Route::middleware(['CheckLoginTime', 'checkDevice'])->group(function () {
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
                // Route::get('list', [Controllers\Admin\DocumentController::class, 'list'])->name('list');
                // Route::get('ajax_list', [Controllers\Admin\DocumentController::class, 'ajax_list'])->name('ajax_list');
                // Route::post('manage_process/{id?}', [Controllers\Admin\DocumentController::class, 'manage_process'])->name('manage_process');
                Route::post('upload_document', [Controllers\Admin\DocumentController::class, 'uploadDocument'])->name('upload');
            });

        });
    });
});
