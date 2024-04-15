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
        Schema::create('before_orders', function (Blueprint $table) {
            $table->id('beforeOrderId');
            $table->unsignedBigInteger('userId');
            $table->unsignedBigInteger('time');
            $table->string('status');
            $table->smallInteger('tableNumber');
            $table->string('isTakeAway');
            $table->text('note')->nullable();
            $table->string('discountCode')->nullable();
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
