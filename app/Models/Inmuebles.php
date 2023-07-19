<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inmuebles extends Model
{
    use HasFactory;

    protected $table = "inmuebles";

    protected $fillable = [
        'titulo',
        'descripcion',
        'm2',
        'm2_construidos',
        'valor_referencia',
        'habitaciones',
        'banos',
        'tipo_vivienda_id',
        'ubicacion',
        'cod_postal',
        'cert_energetico',
        'cert_energetico_elegido',
        'inmobiliaria',
        'estado',
        'vendedor_id',
        'galeria',
        'disponibilidad',
        'otras_caracteristicas',
        'referencia_catastral',

    ];

}
