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
            $table->id('deforeOderId');
            $table->unsignedBigInteger('userId');
            $table->timestamp('time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('status');
            $table->smallInteger('tableNumber');
            $table->boolean('isTakeAway');
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
