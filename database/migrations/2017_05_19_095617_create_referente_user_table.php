<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferenteUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referente_user', function (Blueprint $table) {
			$table->integer('user_id')->unsigned();
            $table->integer('referente_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('referente_id')->references('id')->on('referentes')->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['user_id', 'referente_id']);
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referente_user');
    }
}
