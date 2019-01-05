<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYxFuncTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yx_func', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 64)->default('');
            $table->integer('sort')->default(0);
            $table->unsignedInteger('parent_id')->default(0);
            $table->unsignedTinyInteger('is_feature')->default(0);
            $table->text('picture')->nullable();
            $table->text('background')->nullable();
            $table->string('desc', 100)->nullable();
            $table->timestamps();
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yx_func');
    }
}
