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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes('deleted_at');
            $table->boolean('is_full_payed')->default(true);
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->boolean('has_discount')->default(false);
            $table->enum('discount_type', ['early_payment', 'scholarship'])->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->string('folio_ticket', 14)->unique();
            $table->bigInteger('payment_method_id')->unsigned();
            $table->bigInteger('student_group_id')->unsigned();

            $table->foreign('payment_method_id')->references('id')->on('cat_payment_methods');
            $table->foreign('student_group_id')->references('id')->on('student_groups');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
