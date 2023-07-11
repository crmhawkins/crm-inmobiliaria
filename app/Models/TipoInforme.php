<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoInforme extends Model
{
    use HasFactory;

    protected $table = "tipo_informe";

    protected $fillable = [
        'categoria_id',
        'nombre',
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function categoria()
    {
        return $this->belongsTo("App\Models\CategoriaInforme", "categoria_id", "id");
    }

    public function informes()
    {
        return $this->hasMany("App\Models\Informe", "tipo_informe_id");
    }

}
