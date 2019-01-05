<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_auto_reply', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merchant_id')->index();
            $table->string('match_mode', 16);
            $table->string('keyword', 32);
            $table->string('reply_mode', 16);
            $table->longText('reply');
            $table->timestamp('start_at')->nullable()->index();
            $table->timestamp('end_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('wechat_menu', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merchant_id')->index();
            $table->integer('parent_id')->default(0)->index();
            $table->integer('order')->default(0);
            $table->string('title', 50);
            $table->string('type', 16);
            $table->text('uri')->nullable();
            $table->text('target')->nullable();

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
        Schema::drop('wechat_auto_reply');
        Schema::drop('wechat_menu');
    }
}
