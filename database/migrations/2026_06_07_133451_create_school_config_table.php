<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_config', function (Blueprint $table) {
            $table->id();
            $table->string('school_name', 128);
            $table->string('favicon_path', 256)->nullable();
            $table->string('logo_path', 256)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_config');
    }
};
