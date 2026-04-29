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
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);
            $table->integer('status');
            $table->string('name', length: 128);
            $table->float('amount');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('scholar_year_id');

            $table->foreign('student_id')
                  ->references('id')
                  ->on('students');
            $table->foreign('scholar_year_id')
                  ->references('id')
                  ->on('scholar_years');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
