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
        Schema::create('factura_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->onDelete('cascade');
            $table->string('descripcion');
            $table->decimal('importe', 10, 2);        // precio sin IVA
            $table->integer('iva_tipo');             // 21, 10, 4
            $table->decimal('iva_cantidad', 10, 2);  // cantidad de IVA
            $table->decimal('total_con_iva', 10, 2); // importe + iva
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
        Schema::dropIfExists('factura_items');
    }
};
