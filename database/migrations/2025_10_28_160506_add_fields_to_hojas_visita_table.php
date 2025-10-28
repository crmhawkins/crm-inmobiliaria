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
        if (Schema::hasTable('hojas_visita')) {
            Schema::table('hojas_visita', function (Blueprint $table) {
                if (!Schema::hasColumn('hojas_visita', 'evento_id')) {
                    $table->unsignedBigInteger('evento_id')->nullable()->after('cliente_id');
                }
                if (!Schema::hasColumn('hojas_visita', 'firma')) {
                    $table->string('firma')->nullable()->after('ruta');
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
        Schema::table('hojas_visita', function (Blueprint $table) {
            $table->dropColumn(['evento_id', 'firma']);
        });
    }
};
