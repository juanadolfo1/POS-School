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
        Schema::create('student_groups', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('status');
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('student_id');

            $table->foreign('group_id')
            ->references('id')
            ->on('groups');
            $table->foreign('student_id')
            ->references('id')
            ->on('students');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_groups');
    }
};
