<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBargainLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bargain_log', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id')->index()->comment('项目ID');
            $table->unsignedInteger('merchant_id')->index()->comment('客户ID');
            $table->unsignedInteger('player_id')->index()->comment('选手ID');
            $table->unsignedDecimal('price')->comment('砍掉的价格');
            $table->unsignedInteger('operator_uid')->index()->comment('操作者')->default(0);
            $table->string('ip', 40)->index()->comment('来源IP')->default('');
            $table->string('openid', 64)->index()->comment('邀请人openid')->default('');
            $table->string('name', 64)->index()->comment('邀请人昵称')->default('');
            $table->string('zhuli_openid', 64)->index()->comment('助力者openid')->default('');
            $table->string('zhuli_name', 64)->index()->comment('助力者昵称')->default('');
            $table->text('note')->nullable()->comment('备注');
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
        Schema::dropIfExists('bargain_log');
    }
}
