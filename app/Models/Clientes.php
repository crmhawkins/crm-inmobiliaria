<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    use HasFactory;

    protected $table = "clientes";

    protected $fillable = [
        'nombre_completo',
        'dni',
        'telefono',
        'email',
        'intereses',
        'inmuebles_intereses',
        'inmobiliaria',
    ];

}
