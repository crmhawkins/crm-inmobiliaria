<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $table = "facturas";

    protected $fillable = [
        "cliente",
        "numero_factura",
        "fecha",
        "fecha_vencimiento",
        "articulos",
        "subtotal",
        "total",
        "condiciones",
        "inmobiliaria",
        "ruta_pdf"
    ];
}
