<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDestinoidToDocumentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('documentos', function (Blueprint $table) {
            $table->integer('destino_id')->unsigned()->nullable()->after('tipoAcepta');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('documentos', function (Blueprint $table) {
            $table->dropColumn('destino_id');
        });
    }
}
