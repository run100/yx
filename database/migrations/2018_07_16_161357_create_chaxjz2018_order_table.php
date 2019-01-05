<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChaxjz2018OrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chaxjz2018_order', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('type')->default(1);
            $table->unsignedTinyInteger('status')->default(1);
            $table->string('openid', 32);
            $table->string('prepay_id', 48)->default('');
            $table->string('order_no', 48);         //订单号
            $table->string('trade_no', 48)->default('');     //流水单号，因改价等, 一个业务订单可能对应多个付款单，但同一时间只有一个付款单有效(is_valid = true)
            $table->string('transaction_id', 48)->default('');
            $table->string('name1', 64);
            $table->string('name2', 64)->default('');
            $table->string('phone', 20)->default('');
            $table->text('order')->nullable();
            $table->text('payment')->nullable();
            $table->text('refund')->nullable();
            $table->text('data')->nullable();
            $table->timestamps();

            $table->index('openid');
            $table->index('order_no');
            $table->index('name1');
            $table->index('name2');
            $table->index('phone');
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
        Schema::dropIfExists('chaxjz2018_order');
    }
}
