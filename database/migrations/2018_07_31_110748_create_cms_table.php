<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedTinyInteger('type')->index()->default(0)->comment('新闻类别');
            $table->unsignedInteger('project_id')->index()->comment('项目ID')->default(0);
            $table->string('title', 250)->comment('新闻标题')->default('');
            $table->string('link', 250)->comment('新闻链接')->default('');
            $table->string('pic', 250)->comment('新闻图片')->default('');
            $table->string('video', 250)->comment('视频上传')->default('');
            $table->unsignedTinyInteger('status')->index()->default(0)->comment('新闻状态');

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
        Schema::dropIfExists('cms');
    }
}
