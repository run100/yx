<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoupansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loupans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 64)->comment('楼盘名');
            $table->unsignedInteger('price')->comment('最新价格');
            $table->string('title', 64)->comment('主题');
            $table->string('address')->comment('楼盘地址');
            $table->string('company')->comment('楼盘开发商');
            $table->string('sun_img')->comment('太阳码');
            $table->text('banner')->comment('轮播图');
            $table->text('intro')->comment('楼盘介绍');
            $table->timestamps();

        });

        Schema::create('loupan_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('player_id');
            $table->unsignedInteger('loupan_id');
            $table->char('openid', 32)->comment('openid');
            $table->string('username', 64)->comment('姓名');
            $table->string('mobile', 64)->comment('电话');
            $table->dateTime('view_time')->comment('看房时间');
            $table->string('remark')->comment('备注');

            $table->timestamps();

            $table->index('player_id');
            $table->index('loupan_id');

        });

        Schema::create('loupan_partners', function (Blueprint $table) {
            $table->increments('id');

            $table->string('img')->comment('图片');

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
        Schema::dropIfExists('loupans');
        Schema::dropIfExists('loupan_logs');
        Schema::dropIfExists('loupan_partners');
    }
}
