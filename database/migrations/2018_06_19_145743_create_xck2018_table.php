<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXck2018Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xck2018', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id')->index()->comment('项目ID');
            $table->string('name', 64)->index()->comment('区域/商家名称');
            $table->unsignedDecimal('total_income',10,2)->comment('总收入')->default(0);
            $table->unsignedInteger('parent_id')->index()->comment('父级ID')->default(0);
            $table->unsignedInteger('operator_uid')->index()->comment('操作者')->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('xck2018');
    }
}
