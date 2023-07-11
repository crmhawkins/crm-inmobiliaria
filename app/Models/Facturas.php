<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facturas extends Model
{
    use HasFactory;

    protected $table = "facturas";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'numero_factura',
        'id_presupuesto',
        'fecha_emision',
        'fecha_vencimiento',
        'descripcion',
        'estado',
        'metodo_pago',
        'tipo_documento',
        'documentos',
        'observaciones',
        'precio',
        'precio_iva',

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
