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
        Schema::create('user_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('pending_balance',100,2)->default(0.00);
            $table->double('outstanding_balance',100,2)->default(0.00);
            $table->double('balance',100,2)->default(0.00);
            $table->double('commission', 20, 2)->default(0.00);
            $table->string('commercial_record')->nullable();
            $table->string('commercial_record_image_f')->nullable();
            $table->string('commercial_record_image_b')->nullable();
            $table->string('tax_card')->nullable();
            $table->string('tax_card_image_f')->nullable();
            $table->string('tax_card_image_b')->nullable();
            $table->string('location')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('national_id')->nullable();
            $table->string('national_id_image_f')->nullable();
            $table->string('national_id_image_b')->nullable();
            $table->unsignedBigInteger('track_type')->nullable();
            $table->string('driving_license_number')->nullable();
            $table->string('driving_license_image_f')->nullable();
            $table->string('driving_license_image_b')->nullable();
            $table->string('track_license_number')->nullable();
            $table->string('track_license_image_f')->nullable();
            $table->string('track_license_image_b')->nullable();
            $table->string('track_number')->nullable();
            $table->string('track_image_f')->nullable();
            $table->string('track_image_b')->nullable();
            $table->string('track_image_s')->nullable();
            $table->boolean('revision')->default(0);
            $table->unsignedBigInteger('company_id')->nullable();
            //$table->foreign('company_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('image')->nullable()->default('/uploads/users/default.png');
            $table->string('phone',30)->nullable();
            $table->longText('desc')->nullable();
            $table->longText('notes')->nullable();
            $table->string('type',30);
            $table->double('works_hours', 100, 2)->default(0.00);
            $table->integer('date_of_payment')->default(1);
            $table->string('status', 30)->nullable();
            $table->boolean('vip')->default(0);
            $table->foreignId('user_id')->constrained();
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
        Schema::dropIfExists('user_data');
    }
};
