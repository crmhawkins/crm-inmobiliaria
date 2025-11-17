<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TipoVivienda;
use App\Models\Clientes;

class Inmuebles extends Model
{
    use HasFactory;

    protected $table = "inmuebles";

    protected $fillable = [
        'titulo',
        'descripcion',
        'm2',
        'm2_construidos',
        'valor_referencia',
        'habitaciones',
        'banos',
        'tipo_vivienda_id',
        'ubicacion',
        'cod_postal',
        'cert_energetico',
        'cert_energetico_elegido',
        'inmobiliaria',
        'estado',
        'vendedor_id',
        'galeria',
        'disponibilidad',
        'otras_caracteristicas',
        'referencia_catastral',
        'external_id',

        // Campos nuevos relacionados con Fotocasa
        'building_type_id',
        'building_subtype_id',
        'transaction_type_id',
        'visibility_mode_id',
        'floor_id',
        'orientation_id',
        'has_terrace',
        'terrace_surface',
        'has_heating',
        'heating_type_id',
        'hot_water_type_id',
        'consumption_efficiency_scale',
        'emissions_efficiency_scale',
        'consumption_efficiency_value',
        'emissions_efficiency_value',
        'energy_certificate_status',
        'conservation_status',
        'year_built',
        'furnished',
        'has_elevator',
        'has_wardrobe',
        'has_surveillance',
        'has_equipped_kitchen',
        'has_air_conditioning',
        'has_parking',
        'has_security_door',
        'has_private_garden',
        'has_yard',
        'has_storage_room',
        'has_smoke_outlet',
        'has_community_pool',
        'has_private_pool',
        'has_loading_area',
        'has_24h_access',
        'has_internal_transport',
        'has_alarm',
        'has_access_code',
        'has_free_parking',
        'has_laundry',
        'has_community_area',
        'has_office_kitchen',
        'has_jacuzzi',
        'has_sauna',
        'has_tennis_court',
        'has_gym',
        'has_sports_area',
        'has_children_area',
        'has_home_automation',
        'has_internet',
        'has_suite_bathroom',
        'has_home_appliances',
        'has_oven',
        'has_washing_machine',
        'has_microwave',
        'has_fridge',
        'has_tv',
        'has_parquet',
        'has_stoneware',
        'has_balcony',
        'pets_allowed',
        'nearby_public_transport',
        'land_area',
        'latitude',
        'longitude',
        'mostrar_precio',
        'idealista_property_id',
        'idealista_code',
        'idealista_payload',
        'idealista_synced_at',
    ];

    public function tipoVivienda()
    {
        return $this->belongsTo(TipoVivienda::class, 'tipo_vivienda_id');
    }

    public function vendedor()
    {
        return $this->belongsTo(Clientes::class, 'vendedor_id');
    }

    protected $casts = [
        'idealista_synced_at' => 'datetime',
    ];
}
