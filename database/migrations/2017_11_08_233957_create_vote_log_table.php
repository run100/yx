<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoteLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote_log', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id')->index()->comment('项目ID');
            $table->unsignedInteger('merchant_id')->index()->comment('客户ID');
            $table->unsignedInteger('player_id')->index()->comment('选手ID');
            $table->unsignedInteger('operator_uid')->index()->comment('操作者');
            $table->string('ip', 40)->index()->comment('来源IP')->default('');
            $table->string('phone', 16)->index()->comment('来源Phone')->default('');
            $table->string('openid', 64)->index()->comment('来源OpenID')->default('');
            $table->string('field', 16)->comment('字段');
            $table->integer('incr')->comment('增量');
            $table->string('note', 64)->comment('备注');
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
        Schema::dropIfExists('vote_log');
    }
}
