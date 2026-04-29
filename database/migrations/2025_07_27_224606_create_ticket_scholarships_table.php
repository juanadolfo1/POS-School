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
        Schema::create('ticket_scholarships', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes('deleted_at');
            $table->bigInteger('scholarship_id');
            $table->bigInteger('ticket_id');

            $table->foreign('scholarship_id')->references('id')->on('scholarships');
            $table->foreign('ticket_id')->references('id')->on('tickets');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_scholarships');
    }
};
