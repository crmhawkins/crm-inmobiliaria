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
        Schema::create('orden_log', function (Blueprint $table) {
            $table->id();
            $table->integer('tarea_id')->nullable();;
            $table->integer('trabajador_id')->nullable();;
            $table->timestamp('fecha_inicio')->nullable();;
            $table->timestamp('fecha_fin')->nullable();;
            $table->string('estado')->nullable();;
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
        Schema::dropIfExists('orden_log');
    }
};
