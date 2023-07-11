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
        Schema::create('ecotasas', function (Blueprint $table) {
            $table->id();
            $table->string("nombre");
            $table->double("valor");
            $table->integer("peso_min");
            $table->integer("peso_max");
            $table->boolean("diametro_mayor_1400");
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
        Schema::dropIfExists('ecotasas');
    }
};
