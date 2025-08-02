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
        Schema::create('order_status_qr', function (Blueprint $table) {
          $table->id();
        $table->unsignedBigInteger('order_id');
        $table->enum('type', ['pick_up', 'receive']);
        $table->json('payload');
        $table->string('signature');
        $table->timestamp('expires_at')->nullable();
        $table->timestamp('used_at')->nullable();
        $table->timestamps();

        $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_status_qr');
    }
};
