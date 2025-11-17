<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('idealista_contact_id')->nullable()->after('inmobiliaria')->unique();
            $table->string('telefono_prefijo', 10)->nullable()->after('telefono');
            $table->boolean('idealista_es_agente')->default(false)->after('inmobiliaria');
            $table->boolean('idealista_es_activo')->default(true)->after('idealista_es_agente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn([
                'idealista_contact_id',
                'telefono_prefijo',
                'idealista_es_agente',
                'idealista_es_activo',
            ]);
        });
    }
};

