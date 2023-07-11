<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoProducto extends Model
{
    use HasFactory;
    protected $table = "tipo_productos";

    protected $fillable = [
        'tipo_producto',
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

    public function categorias()
    {
        return $this->hasMany("App\Models\ProductosCategories", "tipo_producto", "id");
    }
}
