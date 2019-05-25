<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConvenioValidadorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('convenio_validador', function (Blueprint $table) {
			$table->integer('convenio_id')->unsigned();
            $table->integer('validador_id')->unsigned();

            $table->foreign('convenio_id')->references('id')->on('convenios')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('validador_id')->references('id')->on('validadors')->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['convenio_id', 'validador_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('convenio_validador');
    }
}
