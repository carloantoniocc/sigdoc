<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_docs', function (Blueprint $table) {
            $table->increments('id')->index();
			$table->string('name')->unique();
			$table->smallInteger('flujo'); //1 - Factura ; 2 - Garantia
			$table->boolean('oc')->default(false);
			$table->boolean('asociado')->default(false);
			$table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_docs');
    }
}
