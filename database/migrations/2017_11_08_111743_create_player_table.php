<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id')->index()->comment('项目ID');
            $table->unsignedInteger('merchant_id')->index()->comment('客户ID');
            $table->string('uniqid', 64)->nullable()->index()->comment('唯一识别码');
            $table->longText('info')->comment('用户信息');
            $table->string('str1', 64)->default('')->index()->comment('预留字符型字段1');
            $table->string('str2', 64)->default('')->index()->comment('预留字符型字段2');
            $table->string('str3', 64)->default('')->index()->comment('预留字符型字段3');
            $table->string('str4', 64)->default('')->index()->comment('预留字符型字段4');
            $table->string('str5', 64)->default('')->index()->comment('预留字符型字段5');
            $table->integer('int1')->default(0)->index()->comment('预留数字型字段1');
            $table->integer('int2')->default(0)->index()->comment('预留数字型字段2');
            $table->integer('int3')->default(0)->index()->comment('预留数字型字段3');
            $table->integer('int4')->default(0)->index()->comment('预留数字型字段4');
            $table->integer('int5')->default(0)->index()->comment('预留数字型字段5');
            $table->integer('vote1')->default(0)->comment('预留投票型字段1');
            $table->integer('vote2')->default(0)->comment('预留投票型字段2');
            $table->integer('vote3')->default(0)->comment('预留投票型字段3');
            $table->timestamps();

            $table->unique(['project_id', 'uniqid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player');
    }
}
