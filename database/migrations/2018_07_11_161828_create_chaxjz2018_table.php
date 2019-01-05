<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChaxjz2018Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chaxjz2018', function (Blueprint $table) {
            $table->increments('id');
            $table->text('intro')->nullable()->comment('文字介绍');
            $table->text('img')->nullable()->comment('图片');
            $table->text('voice')->nullable()->comment('语音');
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
        Schema::dropIfExists('chaxjz2018');
    }
}
