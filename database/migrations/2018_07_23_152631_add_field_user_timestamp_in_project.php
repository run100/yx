<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldUserTimestampInProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project', function (Blueprint $table) {
            $table->timestamp('use_start_at')->nullable();
            $table->timestamp('use_end_at')->nullable();
            $table->index('use_start_at');
            $table->index('use_end_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project', function (Blueprint $table) {
            $table->dropColumn(['use_start_at', 'use_end_at']);
            $table->dropIndex(['use_start_at', 'use_end_at']);
        });
    }
}
