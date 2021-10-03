<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixTablesIdColumnName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logger', function (Blueprint $table) {
            $table->renameColumn('id_user', 'user_id');
            $table->renameColumn('id_log', 'logger_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logger', function (Blueprint $table) {
            $table->renameColumn('user_id', 'id_user');
            $table->renameColumn('logger_id', 'id_log');
        });
    }
}
