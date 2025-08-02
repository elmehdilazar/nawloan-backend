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
        Schema::create('career_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_en');
            $table->string('category_ar');
            $table->text('category_desc_en');
            $table->text('category_desc_ar');
            $table->string('meta_title_en');
            $table->string('meta_title_ar');
            $table->string('slug_en');
            $table->string('slug_ar');
            $table->text('meta_desc_en');
            $table->text('meta_desc_ar');
            $table->softDeletes();
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
        Schema::dropIfExists('career_categories');
    }
};
