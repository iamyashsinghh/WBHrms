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
// Route::get('mail_view', function () {
//     $data = [
//         'candidate_name' => 'Yash Singh',
//         'position' => 'Software Engineer',
//         'company_name' => 'Wedding Inc.',
//         'start_date' => '2024-12-01',
//         'salary' => '$70,000',
//         'location' => 'New York',
//         'acceptance_deadline' => '2024-12-15',
//         'contact_email' => 'hr@weddinginc.com',
//     ];

//     $emp_code = 'A-2021';
//     $salaryData = Salary::where('emp_code', $emp_code)
//         ->with('salaryType')
//         ->get();
//     $salarySummary = [];
//     foreach ($salaryData as $salary) {
//         $perMonth = $salary->salary;
//         $perAnnum = $salary->salary * 12;
//         $salarySummary[] = [
//             'name' => $salary->salaryType->name,
//             'category' => $salary->salaryType->category,
//             'per_month' => $perMonth,
//             'per_annum' => $perAnnum,
//         ];
//     }

//     $pdf = PDF::loadView('mail.offerletterpdf', compact('data', 'salarySummary'))
//         ->setPaper('a4', 'portrait');

//     return $pdf->download('OfferLetter.pdf');
// });
// Route::get('mail_vie', function () {
//     $data = [
//         'candidate_name' => 'Yash Singh',
//         'position' => 'Software Engineer',
//         'company_name' => 'Wedding Inc.',
//         'start_date' => '2024-12-01',
//         'salary' => '$70,000',
//         'location' => 'New York',
//         'acceptance_deadline' => '2024-12-15',
//         'contact_email' => 'hr@weddinginc.com',
//     ];
//     $emp_code = 'A-2021';
//     $salaryData = Salary::where('emp_code', $emp_code)
//         ->with('salaryType')
//         ->get();
//     $salarySummary = [];
//     foreach ($salaryData as $salary) {
//         $perMonth = $salary->salary;
//         $perAnnum = $salary->salary * 12;
//         $salarySummary[] = [
//             'name' => $salary->salaryType->name,
//             'category' => $salary->salaryType->category,
//             'per_month' => $perMonth,
//             'per_annum' => $perAnnum,
//         ];
//     }
//     return view('mail.offerletterpdf', compact('data', 'salarySummary'));
// });



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

                Route::post('updateEmploymentInfo', [Controllers\EmployeeDataController::class, 'updateEmploymentInfo'])->name('updateEmploymentInfo');
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
            | Admin ENV CONTROLLER
            |--------------------------------------------------------------------------
            */
            Route::prefix('/env')->name('env.')->group(function () {
                Route::get('/env-update', [Controllers\Admin\EnvController::class, 'index'])->name('index');
                Route::post('/env-update', [Controllers\Admin\EnvController::class, 'update'])->name('update');
            });
        });
    });
});
