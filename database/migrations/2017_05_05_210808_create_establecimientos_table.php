<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstablecimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('establecimientos', function (Blueprint $table) {
            $table->increments('id')->index();
			$table->string('code')->unique();
			$table->string('name')->unique();
			$table->integer('tipo_id')->unsigned();
            $table->integer('comuna_id')->unsigned();
			$table->string('direccion');
			$table->double('X', 15, 8)->nullable();
			$table->double('Y', 15, 8)->nullable();
			$table->boolean('active')->default(true);
            $table->timestamps();
			
			$table->foreign('tipo_id')->references('id')->on('tipo_estabs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('comuna_id')->references('id')->on('comunas')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('establecimientos');
    }
}
