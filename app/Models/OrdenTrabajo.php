<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenTrabajo extends Model
{
    use HasFactory;

    protected $table = "orden_trabajos";

    protected $fillable = [
        'fecha',
        'id_cliente',
        'id_presupuesto',
        'observaciones',
        'trabajos_solicitados',
        'trabajos_realizar',
        'operarios',
        'descripcion',
        'documentos',
        'estado',
        'lista_tiempo',
        'operarios_tiempo',
        'danos_localizados',

    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function presupuesto()
    {
        return $this->hasOne(Presupuesto::class, 'id', 'id_presupuesto');
    }

    public function logs()
    {
        return $this->hasMany(OrdenLog::class, 'tarea_id');
    }

    public function logsEnCurso()
{
    return $this->hasMany(OrdenLog::class, 'tarea_id')->where('estado', 'En curso');
}

    public function trabajadores()
    {
        return $this->belongsToMany(User::class, 'orden_asignacion', 'tarea_id', 'trabajador_id');
    }
}
