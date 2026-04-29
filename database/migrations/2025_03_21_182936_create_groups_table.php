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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);
            $table->integer('status');
            $table->string('label', length:6);
            $table->unsignedBigInteger('scholar_year_id');
            $table->unsignedBigInteger('academic_level_id');

            $table->foreign('scholar_year_id')
                  ->references('id')
                  ->on('scholar_years');
            $table->foreign('academic_level_id')
                  ->references('id')
                  ->on('cat_academic_levels');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
