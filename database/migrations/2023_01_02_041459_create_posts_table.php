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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->text('images');
            $table->string('name');
            $table->integer('category_id');
            $table->bigInteger('price');
            $table->string('mmk',20);
            $table->tinyInteger('adjust_price')->nullable();
            $table->longText('additional')->nullable();
            $table->longText('description');
            $table->tinyInteger('status')->default(1);
            $table->bigInteger('view')->nullable();
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
        Schema::dropIfExists('posts');
    }
};
