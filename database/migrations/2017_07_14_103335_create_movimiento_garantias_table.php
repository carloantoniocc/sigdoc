<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovimientoGarantiasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimiento_garantias', function (Blueprint $table) {
            $table->increments('id')->index();
			$table->integer('garantia_id')->unsigned();
			$table->date('fechaVencimiento');
			$table->string('estado');
			$table->string('observacion')->nullable();
			$table->integer('user_id')->unsigned();
			$table->boolean('active')->default(true);
            $table->timestamps();
			
			$table->foreign('garantia_id')->references('id')->on('garantias')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movimiento_garantias');
    }
}
