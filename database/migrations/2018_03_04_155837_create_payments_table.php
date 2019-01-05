<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant', function (Blueprint $table) {
            $table->text('mch_key')->nullable();
        });

        //即是流水表也是订单表
        \Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merchant_id');
            $table->unsignedInteger('project_id');
            $table->boolean('is_valid')->default(true);
            $table->string('type', 32)->default('');
            $table->string('openid', 32);
            $table->string('prepay_id', 48)->default('');
            $table->string('order_no', 48);         //订单号
            $table->string('trade_no', 48);     //流水单号，因改价等, 一个业务订单可能对应多个付款单，但同一时间只有一个付款单有效(is_valid = true)
            $table->string('transaction_id', 48)->default('');
            $table->text('data');
            $table->text('order')->nullable();
            $table->text('payment')->nullable();
            $table->text('refund')->nullable();
            $table->timestamps();

            $table->index(['merchant_id', 'openid']);
            $table->index(['project_id', 'openid']);
            $table->index(['order_no', 'is_valid']);
            $table->index('trade_no');
            $table->index('prepay_id');
            $table->index('type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchant', function (Blueprint $table) {
            $table->dropColumn('mch_key');
        });

        Schema::dropIfExists('payments');
    }
}
