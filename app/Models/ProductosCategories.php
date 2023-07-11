<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductosCategories extends Model
{
    use HasFactory;

    protected $table = "productos_categories";

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

    public function tipo():BelongsTo
    {
        return $this->belongsTo("App\Models\TipoProducto");
    }

    public function productos():HasMany
    {
        return $this->HasMany("App\Models\Productos");
    }
}
