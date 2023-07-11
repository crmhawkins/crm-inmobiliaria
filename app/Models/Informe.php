<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Informe extends Model
{
    use HasFactory;

    protected $table = "informe";

    protected $fillable = [
        'fecha_creacion',
        'tipo_informe_id',
        'ruta_archivo',
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function tipo()
    {
        return $this->belongsTo("App\Models\TipoInforme", "tipo_informe_id", "id");
    }

}
