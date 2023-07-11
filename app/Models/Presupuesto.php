<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presupuesto extends Model
{
    use HasFactory;

    protected $table = "presupuestos";

    protected $fillable = [
        'numero_presupuesto',
        'fecha_emision',
        'cliente_id',
        'estado',
        'matricula',
        'kilometros',
        'trabajador_id',
        'listaArticulos',
        'precio',
        'origen',
        'observaciones',
        'modelo',
        'marca',
        'servicio',
        'vehiculo_renting'

    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }
    public function cliente()
    {
        return $this->belongsTo(Clients::class, 'cliente_id', 'id');
    }
}
