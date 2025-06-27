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
        // En una migración de alteración
        Schema::table('clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->change(); // IMPORTANTE: requiere doctrine/dbal
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
