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
    Schema::create('orders', function (Blueprint $table) {
        $table->id('orderId');
        $table->unsignedBigInteger('userId');
        $table->timestamp('time');
        $table->string('sdt');
        $table->text('note');
        $table->integer('numberProduct');
        $table->double('totalBill');
        $table->double('discountPayment');
        $table->smallInteger('numberTable');
        $table->timestamps();
        $table->foreign('userId')->references('id')->on('users');
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
