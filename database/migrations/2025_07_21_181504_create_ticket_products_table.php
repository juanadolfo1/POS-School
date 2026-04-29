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
        Schema::create('ticket_products', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes('deleted_at');
            $table->integer('quantity')->default(1);
            $table->float('discount');
            $table->float('total');
            $table->bigInteger('ticket_id')->unsigned();
            $table->bigInteger('pay_concept_id')->unsigned();

            $table->foreign('ticket_id')->references('id')->on('tickets');
            $table->foreign('pay_concept_id')->references('id')->on('cat_pay_concepts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_products');
    }
};
