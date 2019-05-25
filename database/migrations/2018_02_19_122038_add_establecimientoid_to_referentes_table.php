<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEstablecimientoidToReferentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('referentes', function (Blueprint $table) {
            $table->integer('establecimiento_id')->unsigned()->default(100)->after('name');
			
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
        Schema::table('referentes', function (Blueprint $table) {
            $table->dropColumn('establecimiento_id');
        });
    }
}
