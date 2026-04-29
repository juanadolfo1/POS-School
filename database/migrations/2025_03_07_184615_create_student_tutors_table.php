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
        Schema::create('student_tutors', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes('deleted_at');
            $table->integer('status');
            $table->char('tutor_type', length: 1);
            $table->string('relation', length: 32);
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('person_id');

            $table->foreign('student_id')
                  ->references('id')
                  ->on('students');
            $table->foreign('person_id')
                  ->references('id')
                  ->on('people');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_tutors');
    }
};
