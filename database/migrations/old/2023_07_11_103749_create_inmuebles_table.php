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
        Schema::create('inmuebles', function (Blueprint $table) {
            $table->id();
            $table->string("titulo")->nullable();
            $table->string("descripcion")->nullable();
            $table->integer("m2")->nullable();
            $table->integer("m2_construidos")->nullable();
            $table->decimal("valor_referencia")->nullable();
            $table->integer("habitaciones")->nullable();
            $table->integer("banos")->nullable();
            $table->string("tipo_vivienda_id")->nullable();
            $table->string("vendedor_id")->nullable();
            $table->string("ubicacion")->nullable();
            $table->string("cod_postal")->nullable();
            $table->boolean("cert_energetico")->nullable();
            $table->string("cert_energetico_elegido")->nullable();
            $table->boolean("inmobiliaria")->nullable();
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
        Schema::dropIfExists('inmuebles');
    }
};
