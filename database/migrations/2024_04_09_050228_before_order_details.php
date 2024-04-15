<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('before_order_details', function (Blueprint $table) {
            $table->string('size');
            $table->unsignedBigInteger('beforeOrderId');
            $table->unsignedBigInteger('productId');
            $table->unsignedBigInteger('price');
            $table->integer('number');
            $table->timestamps();

            $table->foreign('beforeOrderId')->references('beforeOrderId')->on('before_orders');
            $table->foreign('productId')->references('productId')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
