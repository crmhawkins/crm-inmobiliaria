<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('numero_factura')->unique();
            $table->date('fecha');
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('iva_total', 10, 2)->default(0);
            $table->json('iva_por_seccion')->nullable(); // ej. {21: 42.00, 10: 10.50}
            $table->decimal('total', 10, 2)->default(0);
            $table->text('condiciones')->nullable();
            $table->boolean('inmobiliaria')->nullable(); // true = Sayco, false = Sancer
            $table->string('ruta_pdf')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('facturas');
    }
};
