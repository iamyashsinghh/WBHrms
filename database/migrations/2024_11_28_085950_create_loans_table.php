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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('emp_name');
            $table->string('emp_code');
            $table->string('name');
            $table->longText('desc')->nullable();
            $table->string('principal');
            $table->string('tenure');
            $table->string('air')->comment('Anual intrest rate');
            $table->string('interest_type')->nullable();
            $table->string('disbursement_date')->nullable();
            $table->date('instalment_start_month')->nullable();
            $table->date('monthly_instalment');
            $table->tinyInteger('is_completed')->nullable();
            $table->date('instalment_end_month')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
