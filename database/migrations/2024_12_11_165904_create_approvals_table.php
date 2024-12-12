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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->string('emp_code');
            $table->string('type')->comment('sl, hd, cl, pl, wo');
            $table->date('start')->comment('this can be used for single day and also the string days fot the pl');
            $table->date('end');
            $table->string('emp_desc');
            $table->string('hr_desc');
            $table->tinyInteger('is_approved')->comment('0=>waiting, 1=>approved, 2=>reject');
            $table->timestamp('approved_or_rejected_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
