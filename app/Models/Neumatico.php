<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Neumatico extends Model
{
    use HasFactory;
    protected $table = "neumaticos";

    protected $fillable = [
        'articulo_id',
        'resistencia_rodadura',
        'agarre_mojado',
        'emision_ruido',
        'uso',
        'ancho',
        'serie',
        'llanta',
        'indice_carga',
        'codigo_velocidad',

    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo("App\Models\ProductosCategories");
    }
}

