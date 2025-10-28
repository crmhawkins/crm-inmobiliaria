<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HojaVisita extends Model
{
    use HasFactory;

    protected $table = "hojas_visita";

    protected $fillable = [
        "inmueble_id",
        "cliente_id",
        "fecha",
        "ruta",
        "evento_id",
        "firma",
    ];

    public function inmueble()
    {
        return $this->belongsTo(Inmuebles::class, 'inmueble_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'cliente_id');
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }
}
