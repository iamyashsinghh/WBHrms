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
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('punch_in_address')->nullable();
            $table->string('punch_out_address')->nullable();
            $table->string('punch_in_img')->nullable();
            $table->string('punch_out_img')->nullable();
            $table->string('punch_in_coordinates')->nullable();
            $table->string('punch_out_coordinates')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            //
        });
    }
};
