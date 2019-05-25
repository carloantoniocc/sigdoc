<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRestaTipoDocs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tipo_docs', function (Blueprint $table) {
            $table->boolean('resta')->after('id_sii')->default(false); //Verifica si el tipo de documento suma o resta en el total contable
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tipo_docs', function (Blueprint $table) {
            $table->dropColumn('resta');
        });
    }
}
