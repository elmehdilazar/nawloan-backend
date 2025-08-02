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
        Schema::create('support_centers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->longText('message');
            $table->longText('desc')->nullable();
            $table->longText('notes')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->string('replay')->nullable();
            $table->unsignedBigInteger('replay_by')->nullable();
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
        Schema::dropIfExists('support_centers');
    }
};
