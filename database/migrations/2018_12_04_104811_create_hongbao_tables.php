<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHongbaoTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //红包账单
        Schema::create('hongbao_billings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('player_id');
            $table->string('bill_no', 32)->comment('订单号');
            $table->string('wx_no', 32)->default('')->comment('微信订单号');
            $table->string('openid', 64)->comment('用户openid');
            $table->unsignedDecimal('money', 5, 2)->default(0)->comment('红包金额');
            $table->unsignedTinyInteger('is_error')->default(0)->comment('状态');
            $table->text('data')->comment('微信返回数据');
            $table->timestamps();

            $table->index('project_id');
            $table->index('player_id');
            $table->index('bill_no');
            $table->index('wx_no');
            $table->index('openid');

        });

        //红包记录
        Schema::create('hongbao_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('player_id');
            $table->string('openid', 64)->comment('用户openid');
            $table->string('ip', 40)->comment('来源IP')->default('');
            $table->string('wx_name', 64)->comment('微信昵称')->default('');
            $table->unsignedTinyInteger('is_win')->default(0)->comment('是否中奖');
            $table->unsignedDecimal('money', 5, 2)->default(0)->comment('中奖金额');
            $table->timestamps();

            $table->index('project_id');
            $table->index('player_id');
            $table->index('openid');
            $table->index('ip');
            $table->index('wx_name');
        });

        //助力记录
        Schema::create('hongbao_zhulis', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('player_id');
            $table->string('openid', 64)->comment('选手OpenID');
            $table->string('zhuli_name', 64)->comment('助力者昵称')->default('');
            $table->string('zhuli_openid', 64)->comment('助力者OpenID')->default('');
            $table->string('ip', 40)->comment('来源IP')->default('');
            $table->timestamps();

            $table->index('project_id');
            $table->index('player_id');
            $table->index('openid');
            $table->index('zhuli_name');
            $table->index('zhuli_openid');
            $table->index('ip');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hongbao_billings');
        Schema::dropIfExists('hongbao_logs');
        Schema::dropIfExists('hongbao_zhulis');

    }
}
