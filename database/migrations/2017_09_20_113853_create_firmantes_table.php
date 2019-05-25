<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFirmantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firmantes', function (Blueprint $table) {
            $table->increments('id')->index();
			$table->integer('memo_id'); //1 - jefe de convenios; 2 - jefe gestion financieras;
			$table->integer('user_id')->unsigned();
			$table->integer('establecimiento_id')->unsigned();
			$table->date('fechaDesde');
			$table->date('fechaHasta');
			$table->boolean('active')->default(true);
            $table->timestamps();
			
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
        Schema::dropIfExists('firmantes');
    }
}
