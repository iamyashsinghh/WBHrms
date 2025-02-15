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
            $table->date('end')->nullable();
            $table->text('emp_desc')->nullable();
            $table->text('hr_desc')->nullable();
            $table->tinyInteger('is_approved')->comment('0=>waiting, 1=>approved, 2=>reject')->default(0);
            $table->timestamp('approved_or_rejected_at')->nullable();
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
