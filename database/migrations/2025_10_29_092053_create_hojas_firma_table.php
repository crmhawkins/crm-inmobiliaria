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
        if (!Schema::hasTable('hojas_firma')) {
            Schema::create('hojas_firma', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('evento_id');
                $table->text('firma_cliente')->nullable();
                $table->text('firma_agente')->nullable();
                $table->string('nombre_cliente')->nullable();
                $table->string('nombre_agente')->nullable();
                $table->text('observaciones')->nullable();
                $table->string('ruta_pdf')->nullable();
                $table->timestamp('fecha_firma')->nullable();
                $table->timestamps();
                
                // Intentar crear la clave foránea solo si la tabla eventos tiene el tipo correcto
                try {
                    $table->foreign('evento_id')->references('id')->on('eventos')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Si falla, solo crear el índice
                    $table->index('evento_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hojas_firma');
    }
};
