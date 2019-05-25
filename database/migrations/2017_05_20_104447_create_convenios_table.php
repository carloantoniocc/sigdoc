<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConveniosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('convenios', function (Blueprint $table) {
            $table->increments('id')->index();
			$table->integer('proveedor_id')->unsigned();
			$table->integer('tipoCompra_id')->unsigned();
			$table->string('identificador');
			$table->integer('referente_id')->unsigned();
			$table->string('observacion')->nullable();
			$table->boolean('active')->default(true);
            $table->timestamps();
			
			$table->foreign('proveedor_id')->references('id')->on('proveedors')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('tipoCompra_id')->references('id')->on('tipo_compras')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('referente_id')->references('id')->on('referentes')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('convenios');
    }
}
