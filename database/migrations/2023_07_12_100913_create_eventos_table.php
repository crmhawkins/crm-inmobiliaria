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
        Schema::dropIfExists('eventos');
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string("titulo")->nullable();
            $table->string("descripcion")->nullable();
            $table->timestamp("fecha_inicio")->nullable();
            $table->timestamp("fecha_fin")->nullable();
            $table->string("tipo_tarea")->nullable();
            $table->integer("cliente_id")->nullable();
            $table->integer("inmueble_id")->nullable();
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
        Schema::dropIfExists('eventos');
    }
};
