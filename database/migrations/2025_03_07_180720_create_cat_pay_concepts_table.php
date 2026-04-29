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
        Schema::create('cat_pay_concepts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('status');
            $table->softDeletes('deleted_at', precision: 0);
            $table->string('label', length: 128);
            $table->decimal('amount', 10, 2);
            $table->decimal('discount_amount', 10, 2);
            $table->date('last_day_with_discount')->nullable();
            $table->enum('pay_concept_type', ['service', 'tuition']);
            $table->unsignedBigInteger('scholar_year_id');
            $table->unsignedBigInteger('id_cat_academic_level');

            $table->foreign('scholar_year_id')
                  ->references('id')
                  ->on('scholar_years');
            $table->foreign('id_cat_academic_level')
                  ->references('id')
                  ->on('cat_academic_levels');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_pay_concepts');
    }
};
