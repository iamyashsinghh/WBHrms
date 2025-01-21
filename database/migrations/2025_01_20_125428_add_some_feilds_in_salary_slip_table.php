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
        Schema::table('salary_slip', function (Blueprint $table) {
            $table->string('emp_code')->nullable();
            $table->string('created_by')->nullable();
            $table->string('is_paid')->nullable();
            $table->string('path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_slip', function (Blueprint $table) {
            //
        });
    }
};
