<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrizesLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prizes_log', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('player_id')->comment('选手 ID');
            $table->unsignedInteger('project_id')->comment('项目 ID');
            $table->char('field', 5)->comment('奖品KEY');
            $table->unsignedTinyInteger('is_win')->comment('是否中奖')->default(0);
            $table->unsignedTinyInteger('is_draw')->comment('是否领取')->default(0);
            $table->string('ip', 40)->index()->comment('来源IP')->default('');
            $table->string('openid', 64)->index()->comment('来源OpenID')->default('');
            $table->string('wx_name', 64)->index()->comment('微信昵称')->default('');
            $table->string('draw_info')->comment('领奖信息(券码，红包金额...)')->default('');
            $table->string('name')->comment('奖品名')->default('');
            $table->unsignedTinyInteger('type')->comment('奖品类型(1实物,2红包,3券码)')->default(1);
            $table->string('tip')->comment(' 中奖提示')->default('');
            $table->string('operate_id')->comment('操作者ID')->default(0);
            $table->unsignedInteger('operator_uid')->index()->comment('操作者')->default(0);
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
        Schema::dropIfExists('prizes_log');
    }
}
