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
        Schema::create('discount', function (Blueprint $table) {
            $table->id();
            $table->timestamp('startTime');
            $table->timestamp('endTime');
            $table->string('name');
            $table->string('code')->unique();
            $table->unsignedBigInteger('productId');
            $table->timestamps();

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
