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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('course');
            $table->string('institute');
            $table->integer('total_hours')->default(100);
            $table->float('used_hours')->default(0);
            $table->float('remaining_hours')->default(100);
            $table->string('semester');
            $table->string('semester_year');
            $table->boolean('is_logged_in')->default(false);
            $table->timestamp('login_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
