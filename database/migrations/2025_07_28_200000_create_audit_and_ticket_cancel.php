<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Auditoría
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('action', 64)->comment('create, update, delete, cancel, enroll, withdraw, reactivate');
            $table->string('entity', 64)->comment('ticket, student, payment, etc.');
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        // Cancelación de tickets
        Schema::table('tickets', function (Blueprint $table) {
            $table->boolean('is_cancelled')->default(false)->after('student_group_id');
            $table->string('cancel_reason', 255)->nullable()->after('is_cancelled');
            $table->timestamp('cancelled_at')->nullable()->after('cancel_reason');
            $table->unsignedBigInteger('cancelled_by')->nullable()->after('cancelled_at');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['is_cancelled', 'cancel_reason', 'cancelled_at', 'cancelled_by']);
        });
        Schema::dropIfExists('audit_logs');
    }
};
