<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('vat', 12, 2)->nullable();
            $table->double('ton_price', 12, 2)->nullable();
            $table->double('price', 100, 2)->nullable();
            $table->double('sub_total', 100, 2)->nullable();
            $table->string('desc')->nullable();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->foreign('driver_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->text('drivers_ids')->nullable();
            $table->string('notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offers');
    }
};
