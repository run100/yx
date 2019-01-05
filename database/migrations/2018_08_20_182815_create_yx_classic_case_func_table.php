<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYxClassicCaseFuncTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yx_classic_case_func', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('case_id');
            $table->unsignedInteger('func_id');
            $table->index('case_id');
            $table->index('func_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yx_classic_case_func');
    }
}
