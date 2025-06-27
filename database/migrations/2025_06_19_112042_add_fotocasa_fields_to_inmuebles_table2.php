<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inmuebles', function (Blueprint $table) {
            $fields = [
                'subtipo_vivienda_id' => 'unsignedBigInteger',
                'tipo_transaccion_id' => 'unsignedBigInteger',
                'visibilidad_id' => 'unsignedBigInteger',
                'planta_id' => 'unsignedBigInteger',
                'orientacion_id' => 'unsignedBigInteger',
                'cert_consumo_eficiencia_escala' => 'unsignedTinyInteger',
                'cert_emisiones_eficiencia_escala' => 'unsignedTinyInteger',
                'cert_consumo_valor' => 'decimal:5,2',
                'cert_emisiones_valor' => 'decimal:5,2',
                'cert_estado' => 'unsignedTinyInteger',
                'conservacion_estado_id' => 'unsignedBigInteger',
                'ano_construccion' => 'year',
                'amueblado' => 'boolean',
                'calefaccion' => 'boolean',
                'tipo_calefaccion_id' => 'unsignedBigInteger',
                'agua_caliente_id' => 'unsignedBigInteger',
                'jardin_privado' => 'boolean',
                'piscina_privada' => 'boolean',
                'piscina_comunitaria' => 'boolean',
                'zona_comunitaria' => 'boolean',
                'garaje' => 'boolean',
                'ascensor' => 'boolean',
                'trastero' => 'boolean',
                'balcon' => 'boolean',
                'terraza' => 'boolean',
                'superficie_terraza' => 'decimal:6,2',
                'lavadero' => 'boolean',
                'internet' => 'boolean',
                'parquet' => 'boolean',
                'electrodomesticos' => 'boolean',
                'aire_acondicionado' => 'boolean',
                'cocina_equipada' => 'boolean',
                'domotica' => 'boolean',
                'tv' => 'boolean',
            ];

            foreach ($fields as $name => $type) {
                if (!Schema::hasColumn('inmuebles', $name)) {
                    [$baseType, $args] = array_pad(explode(':', $type), 2, null);
                    if ($args) {
                        [$precision, $scale] = explode(',', $args);
                        $column = $table->$baseType($name, (int) $precision, (int) $scale);
                    } else {
                        $column = $table->$baseType($name);
                    }
                    $column->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('inmuebles', function (Blueprint $table) {
            $table->dropColumn([
                'subtipo_vivienda_id',
                'tipo_transaccion_id',
                'visibilidad_id',
                'planta_id',
                'orientacion_id',
                'cert_consumo_eficiencia_escala',
                'cert_emisiones_eficiencia_escala',
                'cert_consumo_valor',
                'cert_emisiones_valor',
                'cert_estado',
                'conservacion_estado_id',
                'ano_construccion',
                'amueblado',
                'calefaccion',
                'tipo_calefaccion_id',
                'agua_caliente_id',
                'jardin_privado',
                'piscina_privada',
                'piscina_comunitaria',
                'zona_comunitaria',
                'garaje',
                'ascensor',
                'trastero',
                'balcon',
                'terraza',
                'superficie_terraza',
                'lavadero',
                'internet',
                'parquet',
                'electrodomesticos',
                'aire_acondicionado',
                'cocina_equipada',
                'domotica',
                'tv'
            ]);
        });
    }
};
