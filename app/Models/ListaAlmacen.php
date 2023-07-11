<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ListaAlmacen extends Model
{
    use HasFactory;
    protected $table = "lista_almacenes";

    protected $fillable = [
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

    public function almacenes()
    {
        return $this->hasMany(Almacen::class, 'nombre', 'nombre');
    }

}
