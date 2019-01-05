<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYxClassicCaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yx_classic_case', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 64)->default('');
            $table->text('desc')->nullable();
            $table->string('keywords', 64)->default('');
            $table->text('url')->nullable();
            $table->string('cases', 64)->nullable();
            $table->text('banner')->nullable();
            $table->text('picture')->nullable();
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('purpose_id');
            $table->unsignedInteger('attentions');
            $table->unsignedInteger('participants');
            $table->string('flow1', 64)->default('');
            $table->string('flow2', 64)->default('');
            $table->string('flow3', 64)->default('');
            $table->string('flow4', 64)->default('');
            $table->unsignedTinyInteger('is_top')->default(0);
            $table->unsignedTinyInteger('is_index')->default(0);
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
        Schema::dropIfExists('yx_classic_case');
    }
}
