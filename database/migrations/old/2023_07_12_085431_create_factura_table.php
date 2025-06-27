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
        Schema::dropIfExists('facturas');
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string("cliente");
            $table->string("numero_factura");
            $table->timestamp("fecha");
            $table->string("articulos");
            $table->decimal("subtotal");
            $table->decimal("total");
            $table->string("condiciones");
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
        Schema::dropIfExists('facturas');
    }
};
