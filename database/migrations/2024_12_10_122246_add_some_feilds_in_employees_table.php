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
        Schema::table('employees', function (Blueprint $table) {
            $table->time('punch_in_time')->default('10:00')->nullable();
            $table->time('punch_out_time')->default('18:30')->nullable();
            $table->string('punch_coordinates')->nullable();
            $table->string('cl_left')->default(12)->nullable();
            $table->string('pl_left')->default(10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            //
        });
    }
};
