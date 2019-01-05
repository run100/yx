<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYxTempCaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yx_temp_case', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 64)->default('');
            $table->text('desc')->nullable();
            $table->string('keywords', 64)->default('');
            $table->text('url')->nullable();
            $table->string('cases', 64)->nullable();
            $table->text('picture')->nullable();
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('purpose_id');
            $table->unsignedTinyInteger('is_top')->default(0);
            $table->timestamps();
            $table->index('name');
            $table->index('keywords');
            $table->index('business_id');
            $table->index('purpose_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yx_temp_case');
    }
}
