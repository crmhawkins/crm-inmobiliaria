<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\OrdenTrabajo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_department_id',
        'user_position_id',
        'username',
        'name',
        'surname',
        'role',
        'email',
        'password',
        'image',
        'seniority_years',
        'seniority_months',
        'holidays_days',
        'inactive',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function ordenLogs()
    {
        return $this->hasMany(OrdenLog::class);
    }

    public function tareas()
    {
        return $this->belongsToMany(OrdenTrabajo::class, 'orden_asignacion', 'trabajador_id', 'tarea_id');
    }

    public function tareasEnCurso()
{
    // 'orden_asignacion' es la tabla intermedia,
    // 'trabajador_id' es la clave foránea en la tabla intermedia que se relaciona con este modelo (Trabajador)
    // 'tarea_id' es la clave foránea en la tabla intermedia que se relaciona con el otro modelo (OrdenTrabajo)
    return $this->belongsToMany(OrdenTrabajo::class, 'orden_asignacion', 'trabajador_id', 'tarea_id')->whereHas('logsEnCurso');
}
}
