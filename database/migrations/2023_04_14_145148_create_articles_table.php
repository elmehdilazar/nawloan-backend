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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->date('article_date');
            $table->string('article_en');
            $table->string('article_ar');
            $table->text('article_desc_en');
            $table->text('article_desc_ar');
            $table->string('meta_title_en');
            $table->string('meta_title_ar');
            $table->string('tage_en');
            $table->string('tage_ar');
            $table->text('meta_desc_en');
            $table->text('meta_desc_ar');
            $table->integer('active')->default(1);
            $table->foreignId('user_id')->constrained();
            $table->bigInteger('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('article_categories');

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
        Schema::dropIfExists('articles');
    }
};
