<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToVotelog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vote_log', function (Blueprint $table) {
            $table->dropIndex(['player_id']);
            $table->dropIndex(['project_id']);

            $table->index(['player_id', 'created_at']);
            $table->index(['project_id', 'openid', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
