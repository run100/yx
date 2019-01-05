<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 64)->comment('客户名称');
            $table->string('appid', 24)->unique()->nullable()->comment('服务号AppId');
            $table->text('refresh_token')->nullable()->comment('服务号RefreshToken');
            $table->longText('extras')->comment('授权信息');
            $table->longText('configs')->comment('配置信息');
            $table->string('pre_auth_code', 32)->unique()->nullable()->comment('预授权码');
            $table->timestamp('pre_auth_code_expire_at')->nullable()->comment('预授权码有效期');
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
        Schema::dropIfExists('merchant');
    }
}
