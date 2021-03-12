<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoggerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logger', function (Blueprint $table) {
            $table->id('id_log');
            $table->unsignedTinyInteger('severity')->default(1);
            $table->unsignedInteger('code')->default(0);
            $table->text('message');
            $table->string('object_type', 32)->nullable();
            $table->integer('object_id')->default(0);
            $table->integer('id_user')->default(0);
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
        Schema::dropIfExists('logger');
    }
}
