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
        Schema::table('facturas', function (Blueprint $table) {
            $table->string("tipo_documento")->after("id")->nullable();
            $table->string("documentos")->after("estado")->nullable();
            $table->string("observaciones")->after("metodo_pago")->nullable();
            $table->decimal('precio')->nullable();
            $table->decimal('precio_iva')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn("tipo_documento");
            $table->dropColumn("documentos");
            $table->dropColumn("observaciones");
            $table->dropColumn("precio");
            $table->dropColumn("precio_iva");
        });
    }
};
