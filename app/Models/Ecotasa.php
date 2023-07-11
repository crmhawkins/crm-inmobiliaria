<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ecotasa extends Model
{
    use HasFactory;

    protected $table = "ecotasas";

    protected $fillable = [
        'nombre',
        'valor',
        'peso_min',
        'peso_max',
        'diametro_mayor_1400',
    ];

}
