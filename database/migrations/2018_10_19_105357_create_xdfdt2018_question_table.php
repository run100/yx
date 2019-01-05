<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXdfdt2018QuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * 题库列表(id,name,option,answer,type,obj,img)zt_xdfdt2018_question
     *      题目id    题目id，递增    搜索项
     *      题目名称    题目名称
     *      选项    选项列表（A：0~1、B：1~2、C：2~3、D：3~4、E：4~5）
     *      答案    题目的答案（A：0~1）
     *      题型    （1:单选 2:多选）   搜索项
     *      对象    （1:小学 2:中学 ）   搜索项
     *      图片    题目是否包含图片    有图片直接显示，没有则不显示
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xdfdt2018_question', function (Blueprint $table) {
            $table->increments('id')->comment("题目id");

            $table->string('title', 100)->nullable(false)->default('')->comment("题目名称");
            $table->string('option')->nullable(false)->default('')->comment("题目选项 [A:option,B:option2,C:option3,D:option4]");
            $table->string('answer')->nullable(false)->default('')->comment("题目答案 [A,B]");
            $table->smallInteger('type')->nullable(false)->default(0)->comment("题型 1:单选|2:多选");
            $table->smallInteger('obj_id')->nullable(false)->default(0)->comment("所属对象 1小学|2中学");
            $table->longText('imager')->comment("图片 [path1,path2]");

            $table->timestamps();
            $table->index('title');
            $table->index('type');
            $table->index('obj_id');
            //$table->comment='题库表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('xdfdt2018_question');
    }
}
