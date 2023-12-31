<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    protected $table = "eventos";

    protected $fillable = [
        "titulo",
        "descripcion",
        "fecha_inicio",
        "fecha_fin",
        "tipo_tarea",
        "cliente_id",
        "inmueble_id",
        "inmobiliaria",

    ];
}
