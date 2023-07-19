<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoArras extends Model
{
    use HasFactory;

    protected $table = "contrato_arras";

    protected $fillable = [
        "inmueble_id",
        "ruta",
    ];
}
