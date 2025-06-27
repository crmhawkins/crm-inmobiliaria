<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'numero_factura',
        'fecha',
        'fecha_vencimiento',
        'subtotal',
        'iva_total',
        'iva_por_seccion',
        'total',
        'condiciones',
        'inmobiliaria',
        'ruta_pdf',
    ];

    protected $casts = [
        'iva_por_seccion' => 'array',
    ];

    public function cliente()
    {
        return $this->belongsTo(Clientes::class);
    }

    public function items()
    {
        return $this->hasMany(FacturaItem::class);
    }

    // App\Models\Factura.php
protected $primaryKey = 'id';

public function getRouteKeyName()
{
    return 'id'; // o 'uuid' si fuera el caso
}

}
