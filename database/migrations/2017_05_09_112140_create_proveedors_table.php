<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProveedorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proveedors', function (Blueprint $table) {
            $table->increments('id')->index();
			$table->integer('rut')->unique();
			$table->char('dv',1);
			$table->string('name')->unique();
			$table->string('fantasia')->nullable();
			$table->integer('comuna_id')->unsigned();
			$table->string('direccion');
			$table->string('email')->nullable();
			$table->string('telefono');
			$table->double('X', 15, 8)->nullable();
			$table->double('Y', 15, 8)->nullable();
			$table->boolean('active')->default(true);
            $table->timestamps();
			
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
        Schema::dropIfExists('proveedors');
    }
}
