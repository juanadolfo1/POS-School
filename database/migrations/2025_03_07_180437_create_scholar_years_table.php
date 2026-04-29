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
        Schema::create('scholar_years', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('status');
            $table->softDeletes('deleted_at', precision: 0);
            $table->string('year', length: 12);
            $table->date('starts_at');
            $table->date('ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholar_years');
    }
};
