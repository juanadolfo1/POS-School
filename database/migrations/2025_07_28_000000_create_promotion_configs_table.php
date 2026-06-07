<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotion_configs', function (Blueprint $table) {
            $table->id();
            $table->integer('default_day')->comment('Día del mes límite por defecto');
            $table->foreignId('academic_level_id')->constrained('cat_academic_levels');
            $table->foreignId('scholar_year_id')->constrained('scholar_years');
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['academic_level_id', 'scholar_year_id']);
        });

        Schema::create('promotion_config_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_config_id')->constrained('promotion_configs')->onDelete('cascade');
            $table->integer('month')->comment('Mes (1-12)');
            $table->date('deadline_date')->comment('Fecha límite específica para ese mes');
            $table->timestamps();

            $table->unique(['promotion_config_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_config_overrides');
        Schema::dropIfExists('promotion_configs');
    }
};
