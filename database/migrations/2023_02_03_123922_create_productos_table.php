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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->integer('cod_producto')->nullable();
            $table->string('descripcion')->nullable();
            $table->integer('tipo_producto')->nullable();
            $table->integer('ecotasa')->nullable();
            $table->string('fabricante')->nullable();
            $table->string('etiquetado_eu')->nullable();
            $table->string('estado')->nullable();
            $table->string('categoria_id    ')->nullable();
            $table->integer('precio_baremo')->nullable();
            $table->integer('descuento')->nullable();
            $table->integer('precio_costo-neto')->nullable();
            $table->integer('precio_venta')->nullable();
            $table->integer('stock')->nullable();
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
        Schema::dropIfExists('productos');
    }
};
