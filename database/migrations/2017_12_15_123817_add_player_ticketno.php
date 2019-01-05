<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlayerTicketno extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player', function (Blueprint $table) {
            $table->string('ticket_no', 64)->nullable()->index()->comment('编号');
            $table->tinyInteger('checked')->default(0)->comment('审核状态');
            $table->unique(['project_id', 'ticket_no']);
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
            $table->dropColumn('ticket_no');
            $table->dropUnique(['project_id', 'ticket_no']);
        });
    }
}
