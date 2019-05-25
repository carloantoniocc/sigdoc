<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTipoAceptaToDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('documentos', function($table) {
            $table->boolean('tipoAcepta')->after('memoGlosa')->default(false); //Documento Cargado a través de Acepta falso para todos los documentos no cargados a través de carga masiva no recepcionados
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documentos', function($table) {
            $table->dropColumn('tipoAcepta');
        });
    }
}
