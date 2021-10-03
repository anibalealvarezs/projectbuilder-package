<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('config', function (Blueprint $table) {
            $table->string('name', 254)->nullable();
            $table->text('description')->nullable();
            $table->renameColumn('id_config', 'id');
            $table->renameColumn('key', 'configkey');
            $table->renameColumn('value', 'configvalue');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('config', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('description');
            $table->renameColumn('id', 'id_config');
            $table->renameColumn('configkey', 'key');
            $table->renameColumn('configvalue', 'value');
        });
    }
}
