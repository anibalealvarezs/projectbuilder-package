<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TurnTranslatableColumnsToJsonType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('navigations', function (Blueprint $table) {
            $table->json('name')->change();
        });
        Schema::table('cities', function (Blueprint $table) {
            $table->json('name')->change();
        });
        Schema::table('config', function (Blueprint $table) {
            $table->json('name')->change();
            $table->json('description')->change();
        });
        Schema::table('countries', function (Blueprint $table) {
            $table->json('name')->change();
        });
        Schema::table('languages', function (Blueprint $table) {
            $table->json('name')->change();
        });
        Schema::table('logger', function (Blueprint $table) {
            $table->json('message')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('navigations', function (Blueprint $table) {
            $table->string('name', 254)->change();
        });
        Schema::table('cities', function (Blueprint $table) {
            $table->string('name', 254)->change();
        });
        Schema::table('config', function (Blueprint $table) {
            $table->string('name', 254)->change();
            $table->text('description')->change();
        });
        Schema::table('countries', function (Blueprint $table) {
            $table->string('name', 254)->change();
        });
        Schema::table('languages', function (Blueprint $table) {
            $table->string('name', 254)->change();
        });
        Schema::table('logger', function (Blueprint $table) {
            $table->text('message')->change();
        });
    }
}
