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
        Schema::table('punch_coordinates', function (Blueprint $table) {
            $table->string('place_name')->comment('for role id 2 3 4 it is default office and home')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('punch_coordinates', function (Blueprint $table) {
            //
        });
    }
};
