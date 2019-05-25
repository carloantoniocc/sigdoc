<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGarantiasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('garantias', function (Blueprint $table) {
            $table->increments('id')->index();
			$table->integer('proveedor_id')->unsigned();
			$table->integer('tipoDoc_id')->unsigned();
			$table->integer('banco_id')->unsigned();
			$table->string('nDoc',20);
			$table->integer('moneda_id')->unsigned();
			$table->float('monto', 14, 4);
			$table->integer('objeto_garantia_id')->unsigned();
			$table->string('licitacion');
			$table->date('fechaEmision');
			$table->date('fechaRecepcion');
			$table->date('fechaVencimiento');
			$table->string('nomina',15)->nullable();
			$table->string('archivo')->nullable();
			$table->integer('user_id')->unsigned();
			$table->integer('establecimiento_id')->unsigned();
			$table->smallInteger('estado')->nullable(); //1 - Cobro Documento ; 2 - Devuelve Documento
			$table->string('archivoConformidad')->nullable();
			$table->date('tesoreria_fecha')->nullable();
			$table->string('tesoreria_rut')->nullable();
			$table->string('tesoreria_nombre')->nullable();
			$table->boolean('memo')->default(true); //falso para todos los documentos anteriores a la nueva versión
            $table->timestamps();
			
			$table->foreign('proveedor_id')->references('id')->on('proveedors')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('tipoDoc_id')->references('id')->on('tipo_docs')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('banco_id')->references('id')->on('bancos')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('moneda_id')->references('id')->on('monedas')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('objeto_garantia_id')->references('id')->on('objeto_garantias')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('establecimiento_id')->references('id')->on('establecimientos')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('garantias');
    }
}
