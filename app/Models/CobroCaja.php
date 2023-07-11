<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CobroCaja extends Model
{
    use HasFactory;

    protected $table = "cobro_caja";

    protected $fillable = [
        'fecha',
        'descripcion',
        'cantidad',
        'metodo_pago',
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];
}
