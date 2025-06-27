<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('neumaticos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('articulo_id');
            $table->string('resistencia_rodadura');
            $table->string('agarre_mojado');
            $table->string('emision_ruido');
            $table->string('uso');
            $table->string('ancho');
            $table->string('serie');
            $table->string('llanta');
            $table->string('indice_carga');
            $table->string('codigo_velocidad');
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
        Schema::dropIfExists('neumaticos');
    }
};
