<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HojaVisita extends Model
{
    use HasFactory;

    protected $table = "hojas_visita";

    protected $fillable = [
        "inmueble_id",
        "cliente_id",
        "fecha",
        "ruta",
    ];
}
