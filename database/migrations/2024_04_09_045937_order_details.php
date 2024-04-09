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
    Schema::create('order_details', function (Blueprint $table) {
        $table->id('size');
        $table->unsignedBigInteger('orderId');
        $table->unsignedBigInteger('productId');
        $table->float('price', 8, 2);
        $table->integer('number');
        $table->timestamps();
        $table->foreign('orderId')->references('orderId')->on('orders');
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
