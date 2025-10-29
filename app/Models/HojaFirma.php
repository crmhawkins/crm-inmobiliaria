<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HojaFirma extends Model
{
    use HasFactory;

    protected $table = "hojas_firma";

    protected $fillable = [
        "evento_id",
        "firma_cliente",
        "firma_agente",
        "nombre_cliente",
        "nombre_agente",
        "observaciones",
        "ruta_pdf",
        "fecha_firma",
    ];

    protected $casts = [
        'fecha_firma' => 'datetime',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }
}
