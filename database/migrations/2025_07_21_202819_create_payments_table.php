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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes('deleted_at');
            $table->integer('is_full_payment');
            $table->decimal('paid_amount', 10, 2);
            $table->date('paid_at')->default(now());
            $table->boolean('applied_discount')->default(false);
            $table->bigInteger('ticket_product_id')->unsigned();

            $table->foreign('ticket_product_id')->references('id')->on('ticket_products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
