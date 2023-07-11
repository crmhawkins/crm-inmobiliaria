<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenLog extends Model
{
    use HasFactory;
    protected $table = "orden_log";

    protected $fillable = [
        'tarea_id',
        'trabajador_id',
        'fecha_inicio',
        'fecha_fin',
        'estado',

    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'trabajador_id');
    }

    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class, 'tarea_id');
    }
}
