<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldStr6ToStr10InPlayer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player', function (Blueprint $table) {
            $table->string('str6', 64)->default('')->index()->comment('预留字符型字段6');
            $table->string('str7', 64)->default('')->index()->comment('预留字符型字段7');
            $table->string('str8', 64)->default('')->index()->comment('预留字符型字段8');
            $table->string('str9', 64)->default('')->index()->comment('预留字符型字段9');
            $table->string('str10', 64)->default('')->index()->comment('预留字符型字段10');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('player', function (Blueprint $table) {
            $table->dropColumn(['str6', 'str7', 'str8', 'str9', 'str10']);
        });
    }
}
