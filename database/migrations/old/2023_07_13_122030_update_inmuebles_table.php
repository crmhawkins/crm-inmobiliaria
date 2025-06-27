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
        Schema::table('inmuebles', function (Blueprint $table) {
            $table->string("estado")->nullable();
            $table->string("disponibilidad")->nullable();
            $table->string("galeria")->nullable();
            $table->string("otras_caracteristicas")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inmuebles', function (Blueprint $table) {
            $table->dropColumn("estado");
            $table->dropColumn("disponibilidad");
            $table->dropColumn("galeria");
            $table->dropColumn("otras_caracteristicas");
        });
    }
};
