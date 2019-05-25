<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->increments('id')->index();
			$table->integer('proveedor_id')->unsigned();
			$table->integer('tipoDoc_id')->unsigned();
			$table->integer('establecimiento_id')->unsigned();
			$table->integer('nDoc');
			$table->integer('facAsociada')->nullable();
			$table->date('fechaEmision');
			$table->date('fechaRecepcion');
			$table->date('fechaVencimiento');
			$table->integer('monto');
			$table->string('ordenCompra')->nullable();
			$table->integer('convenio_id')->unsigned()->nullable();
			$table->string('nomina',15)->nullable();
			$table->string('archivo')->nullable();
			$table->integer('user_id')->unsigned();
			$table->integer('devengo_clasificador_id')->unsigned()->nullable();
			$table->date('devengo_fecha')->nullable();
			$table->integer('devengo_memo')->nullable();
			$table->string('devengo_observacion')->nullable();
			$table->integer('pago_operacion')->nullable();
			$table->integer('pago_tipo_id')->unsigned()->nullable();
			$table->integer('pago_cuenta_id')->unsigned()->nullable();
			$table->date('pago_fechaPago')->nullable();
			$table->integer('pago_sigfe')->nullable();
			$table->date('entrega_fecha')->nullable();
			$table->string('entrega_rut')->nullable();
			$table->string('entrega_nombre')->nullable();
			$table->boolean('memoRef')->default(true); //Memo Referente Tecnico falso para todos los documentos anteriores a la nueva versión
			$table->boolean('memoCon')->default(true); //Memo Convenio          falso para todos los documentos anteriores a la nueva versión
            $table->timestamps();
			
			$table->unique(array('nDoc', 'proveedor_id', 'tipoDoc_id'));
			
			$table->foreign('proveedor_id')->references('id')->on('proveedors')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('tipoDoc_id')->references('id')->on('tipo_docs')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('establecimiento_id')->references('id')->on('establecimientos')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('convenio_id')->references('id')->on('convenios')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('pago_tipo_id')->references('id')->on('tipo_pagos')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('pago_cuenta_id')->references('id')->on('cuentas')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('devengo_clasificador_id')->references('id')->on('clasificadors')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documentos');
    }
}
