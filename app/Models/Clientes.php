<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    use HasFactory;

    protected $table = "clientes";

    protected $fillable = [
        'nombre_completo',
        'dni',
        'telefono',
        'email',
        'direccion',
        'intereses',
        'inmuebles_intereses',
        'inmobiliaria',
        'idealista_contact_id',
        'telefono_prefijo',
        'idealista_es_agente',
        'idealista_es_activo',
    ];

    protected $casts = [
        'intereses' => 'array',
        'idealista_es_agente' => 'boolean',
        'idealista_es_activo' => 'boolean',
    ];

    /**
     * Verificar si un inmueble coincide con los intereses del cliente
     */
    public function interesaInmueble($inmueble)
    {
        if (!$this->intereses || empty($this->intereses)) {
            return false;
        }

        $intereses = is_string($this->intereses) ? json_decode($this->intereses, true) : $this->intereses;

        // Verificar habitaciones
        if (isset($intereses['habitaciones_min']) && !empty($intereses['habitaciones_min']) && $inmueble->habitaciones !== null) {
            if ($inmueble->habitaciones < $intereses['habitaciones_min']) {
                return false;
            }
        }
        if (isset($intereses['habitaciones_max']) && !empty($intereses['habitaciones_max']) && $inmueble->habitaciones !== null) {
            if ($inmueble->habitaciones > $intereses['habitaciones_max']) {
                return false;
            }
        }

        // Verificar baños
        if (isset($intereses['banos_min']) && !empty($intereses['banos_min']) && $inmueble->banos !== null) {
            if ($inmueble->banos < $intereses['banos_min']) {
                return false;
            }
        }
        if (isset($intereses['banos_max']) && !empty($intereses['banos_max']) && $inmueble->banos !== null) {
            if ($inmueble->banos > $intereses['banos_max']) {
                return false;
            }
        }

        // Verificar m2
        if (isset($intereses['m2_min']) && !empty($intereses['m2_min']) && $inmueble->m2 !== null) {
            if ($inmueble->m2 < $intereses['m2_min']) {
                return false;
            }
        }
        if (isset($intereses['m2_max']) && !empty($intereses['m2_max']) && $inmueble->m2 !== null) {
            if ($inmueble->m2 > $intereses['m2_max']) {
                return false;
            }
        }

        // Verificar disponibilidad
        if (isset($intereses['disponibilidad']) && !empty($intereses['disponibilidad']) && $inmueble->disponibilidad) {
            if (strtolower($inmueble->disponibilidad) !== strtolower($intereses['disponibilidad'])) {
                return false;
            }
        }

        // Verificar estado
        if (isset($intereses['estado']) && !empty($intereses['estado']) && $inmueble->estado) {
            if (strtolower($inmueble->estado) !== strtolower($intereses['estado'])) {
                return false;
            }
        }

        // Verificar ubicación (búsqueda parcial)
        if (isset($intereses['ubicacion']) && !empty($intereses['ubicacion']) && $inmueble->ubicacion) {
            $ubicacionCliente = strtolower($intereses['ubicacion']);
            $ubicacionInmueble = strtolower($inmueble->ubicacion);
            if (strpos($ubicacionInmueble, $ubicacionCliente) === false && strpos($ubicacionCliente, $ubicacionInmueble) === false) {
                return false;
            }
        }

        return true;
    }

}
