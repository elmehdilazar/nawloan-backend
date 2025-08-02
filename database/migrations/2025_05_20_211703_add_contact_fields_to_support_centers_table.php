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
    Schema::table('support_centers', function (Blueprint $table) {
        $table->string('name')->nullable();
        $table->string('email')->nullable();
        $table->string('phone_code', 10)->nullable();
        $table->string('phone_number')->nullable();
    });
}

public function down()
{
    Schema::table('support_centers', function (Blueprint $table) {
        $table->dropColumn(['name', 'email', 'phone_code', 'phone_number']);
    });
}

    
};
