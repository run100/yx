<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJiziLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jizi_log', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id')->index()->comment('项目ID');
            $table->unsignedInteger('merchant_id')->index()->comment('客户ID');
            $table->unsignedInteger('player_id')->index()->comment('选手ID');
            $table->unsignedInteger('send_id')->index()->comment('赠送者 ID')->default(0);
            $table->unsignedInteger('operator_uid')->index()->comment('操作者')->default(0);
            $table->string('ip', 40)->index()->comment('来源IP')->default('');
            $table->string('openid', 64)->index()->comment('来源OpenID')->default('');
            $table->char('field', 5)->comment('字段');
            $table->text('content')->comment('集字的内容');
            $table->text('note')->nullable()->comment('备注');
            $table->unsignedTinyInteger('type')->default(0)->comment('0加、1减');
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
        Schema::dropIfExists('jizi_log');
    }
}
