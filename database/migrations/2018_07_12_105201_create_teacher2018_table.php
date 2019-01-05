<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeacher2018Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher2018', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->comment('姓名');
            $table->string('phone', 11)->index()->comment('手机号');
            $table->string('ip', 40)->index()->comment('来源IP')->default('');
            $table->string('openid', 64)->index()->comment('评论人openid')->default('');
            $table->string('wx_name', 64)->index()->comment('评论人昵称')->default('');
            $table->string('wx_poster', 255)->comment('评论人头像')->default('');
            $table->string('content')->comment('评论内容')->default('');
            $table->unsignedTinyInteger('is_check')->index()->comment('是否审核通过')->default(0);
            $table->unsignedInteger('operator_uid')->index()->comment('操作者')->default(0);
            $table->text('note')->nullable()->comment('备注');
            $table->unsignedInteger('zan_count')->index()->comment('点赞数量');
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
        Schema::dropIfExists('teacher2018');
    }
}
