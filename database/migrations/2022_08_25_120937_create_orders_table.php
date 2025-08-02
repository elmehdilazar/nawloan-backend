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
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('car_id')->constrained();
            $table->string('pick_up_address');
            $table->string('pick_up_late');
            $table->string('pick_up_long');
            $table->string('drop_of_address');
            $table->string('drop_of_late');
            $table->string('drop_of_long');
            $table->foreignId('shipment_type_id')->constrained();
            $table->Text('shipment_details')->nullable();
            $table->foreignId('payment_method_id')->constrained();
            $table->boolean('spoil_quickly')->default(0);
            $table->boolean('breakable')->default(0);
            $table->string('size',30);
            $table->unsignedBigInteger('weight_ton');
            $table->double('ton_price', 30, 2);
            $table->double('total_price', 60, 2);
            $table->dateTime('shipping_date')->nullable();
            $table->string('status')->default('pend');
            $table->unsignedBigInteger('service_provider')->nullable();
            $table->unsignedBigInteger('offer_id')->nullable();
            $table->text('drivers_ids')->nullable();
            $table->longText('desc')->nullable();
            $table->longText('notes')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
