<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentoValidadorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documento_validadors', function (Blueprint $table) {
            $table->increments('id')->index();
			$table->integer('documento_id')->unsigned();
            $table->integer('validador_id')->unsigned();
			$table->string('archivo');
			$table->boolean('active')->default(true);
            $table->timestamps();
			
			$table->foreign('documento_id')->references('id')->on('documentos')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('validador_id')->references('id')->on('validadors')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documento_validadors');
    }
}
