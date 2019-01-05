<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXdfdt2018SchoolTable extends Migration
{
    /**
     * Run the migrations.
     *
     *      ( id,name,start_time,end_time,'',create_time)zt_xdfdt2018_school
     *      学校id    学校id，递增、倒序排列
     *      学校名称    学校名称    搜索项
     *      开始时间    时间显示（格式如：2018-12-12  20:00:00）
     *      结束时间    时间显示（格式如：2018-12-12  21:00:00）
     *      创建时间    时间显示（格式如：2018-12-12  21:00:00）
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xdfdt2018_school', function (Blueprint $table) {
            $table->increments('id')->comment('学校id');
            $table->string('name',50)->nullable(false)->default('')->comment('学校名称');
            $table->timestamp('start_time')->default(\DB::raw('CURRENT_TIMESTAMP'))->comment('开始时间');
            $table->timestamp('end_time')->default(\DB::raw('CURRENT_TIMESTAMP'))->comment('结束时间');
            $table->timestamp('create_time')->default(\DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
            $table->timestamps();
            //$table->comment='学校表';
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('xdfdt2018_school');
    }
}
