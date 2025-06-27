<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class FacturaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'factura_id',
        'descripcion',
        'importe',
        'iva_tipo',
        'iva_cantidad',
        'total_con_iva'
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }
}
