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
        Schema::create('order_accountants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('order_id')->constrained();
            $table->double('vat')->default(0.00);
            $table->double('service_provider_amount')->default(0.00);
            $table->double('service_seeker_fee')->default(0.00);
            $table->double('service_provider_commission')->default(0.00);
            $table->double('fine')->default(0.00);
            $table->double('operating_costs')->default(0.00);
            $table->double('expenses')->default(0.00);
            $table->double('diesel_cost')->default(0.00);
            $table->string('status',50)->default('wait');
            $table->boolean('active')->default(1);
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
        Schema::dropIfExists('order_accountants');
    }
};
