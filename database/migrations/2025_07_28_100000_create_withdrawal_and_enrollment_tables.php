<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bajas de alumnos
        Schema::create('student_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('scholar_year_id')->constrained('scholar_years');
            $table->enum('type', ['temporal', 'definitiva']);
            $table->string('reason', 255);
            $table->date('effective_date');
            $table->date('reactivated_at')->nullable();
            $table->integer('status')->default(1)->comment('1=activa, 0=reactivada');
            $table->timestamps();
            $table->softDeletes();
        });

        // Configuración de promoción de grados (para saber 4A → 5A)
        Schema::create('grade_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_level_id')->constrained('cat_academic_levels');
            $table->string('from_grade', 6)->comment('Grado origen (ej: 4A)');
            $table->string('to_grade', 6)->comment('Grado destino (ej: 5A)');
            $table->boolean('is_final_grade')->default(false)->comment('true = egresa del nivel');
            $table->timestamps();
        });

        // Historial de reinscripciones masivas
        Schema::create('enrollment_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_scholar_year_id')->constrained('scholar_years');
            $table->foreignId('to_scholar_year_id')->constrained('scholar_years');
            $table->foreignId('academic_level_id')->constrained('cat_academic_levels');
            $table->integer('students_promoted')->default(0);
            $table->integer('students_graduated')->default(0);
            $table->integer('students_excluded')->default(0)->comment('Bajas no reinscribibles');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_processes');
        Schema::dropIfExists('grade_promotions');
        Schema::dropIfExists('student_withdrawals');
    }
};
