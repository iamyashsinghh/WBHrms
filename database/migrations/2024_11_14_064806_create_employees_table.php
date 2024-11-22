<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('emp_code');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->tinyInteger('role_id');
            $table->date('dob')->nullable()->comment('Date of Birth');
            $table->enum('gender',['male', 'female', 'others'])->nullable();
            $table->enum('marital_status', [1,2,3,4])->nullable()->comment('1=> Single, 2=>Married, 3=>Divorced, 4=>No information');
            $table->string('nationality')->nullable();
            $table->string('emp_type')->nullable()->comment('1=>Fulltime, 2=>Intern');
            $table->date('doj')->nullable()->comment('Date of joining');
            $table->string('employee_designation')->nullable()->comment('Profile of employee');
            $table->string('department')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('profile_img')->nullable();
            $table->string('office_number')->nullable();
            $table->string('office_email')->nullable();
            $table->string('office_email_password')->nullable();
            $table->text('office_email_recovery_info')->nullable();
            $table->text('permanent_address')->nullable();
            $table->text('current_address')->nullable();
            $table->integer('reporting_manager')->nullable();
            $table->string('e_phone')->nullable();
            $table->string('e_name')->nullable();
            $table->string('e_relation')->nullable();
            $table->text('e_address')->nullable();
            $table->text('medical_condition')->nullable();
            $table->text('bank_name')->nullable();
            $table->text('branch_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('crm_id')->nullable();
            $table->string('crm_id_2')->nullable();
            $table->string('holder_name')->nullable();
            $table->string('all_info_filled')->nullable();
            $table->enum('can_edit_info', [0,1])->default(0)->nullable()->comment('0=>No, 1=>Yes');
            $table->string('login_verify')->nullable();
            $table->tinyInteger('can_add_device')->nullable();
            $table->tinyInteger('is_active')->nullable();
            $table->string('status')->nullable()->comment('active', 'resign', 'terminate', 'on_leave');
            $table->softDeletes();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
