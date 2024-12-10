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
            $table->time('puch_in_time')->default('10:00')->nullable();
            $table->time('puch_out_time')->default('18:30')->nullable();
            $table->string('punch_coordinates')->nullable();
            $table->string('cl_left')->default(20)->nullable();
            $table->string('cl_used')->default(0)->nullable();
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
