<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Almacen extends Model
{
    use HasFactory;
    protected $table = "almacenes";

    protected $fillable = [
        'nombre',
        'cod_producto',
        'existencias',
        'existencias_almacenes',
        'existencias_depositos',
        'no_mueve_existencias',
        'actualizado',

    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'cod_producto');
    }

    public function listaAlmacen()
    {
        return $this->belongsTo(ListaAlmacen::class, 'nombre', 'nombre');
    }

}
