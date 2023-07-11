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
        Schema::dropIfExists('orden_trabajos');

        Schema::create('orden_trabajos', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha')->nullable();
            $table->integer('id_cliente')->nullable();
            $table->integer('id_presupuesto')->nullable();
            $table->string('observaciones')->nullable();
            $table->string('trabajos_solicitados')->nullable();
            $table->string('trabajos_realizar')->nullable();
            $table->string('operarios')->nullable();
            $table->string('operarios_tiempo')->nullable();
            $table->string('danos_localizados')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orden_trabajos');
    }
};
