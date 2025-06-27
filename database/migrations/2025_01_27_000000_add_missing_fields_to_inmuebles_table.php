<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inmuebles', function (Blueprint $table) {
            // Campos básicos que faltan
            if (!Schema::hasColumn('inmuebles', 'estado')) {
                $table->string('estado')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'disponibilidad')) {
                $table->string('disponibilidad')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'galeria')) {
                $table->json('galeria')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'otras_caracteristicas')) {
                $table->json('otras_caracteristicas')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'referencia_catastral')) {
                $table->string('referencia_catastral')->nullable();
            }

            // Campos de Fotocasa que faltan según el modelo
            if (!Schema::hasColumn('inmuebles', 'building_type_id')) {
                $table->unsignedBigInteger('building_type_id')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'building_subtype_id')) {
                $table->unsignedBigInteger('building_subtype_id')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'transaction_type_id')) {
                $table->unsignedBigInteger('transaction_type_id')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'visibility_mode_id')) {
                $table->unsignedBigInteger('visibility_mode_id')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'floor_id')) {
                $table->unsignedBigInteger('floor_id')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'orientation_id')) {
                $table->unsignedBigInteger('orientation_id')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_terrace')) {
                $table->boolean('has_terrace')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'terrace_surface')) {
                $table->decimal('terrace_surface', 6, 2)->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_heating')) {
                $table->boolean('has_heating')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'heating_type_id')) {
                $table->unsignedBigInteger('heating_type_id')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'hot_water_type_id')) {
                $table->unsignedBigInteger('hot_water_type_id')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'consumption_efficiency_scale')) {
                $table->unsignedTinyInteger('consumption_efficiency_scale')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'emissions_efficiency_scale')) {
                $table->unsignedTinyInteger('emissions_efficiency_scale')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'consumption_efficiency_value')) {
                $table->decimal('consumption_efficiency_value', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'emissions_efficiency_value')) {
                $table->decimal('emissions_efficiency_value', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'energy_certificate_status')) {
                $table->string('energy_certificate_status')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'conservation_status')) {
                $table->string('conservation_status')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'year_built')) {
                $table->year('year_built')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'furnished')) {
                $table->boolean('furnished')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_elevator')) {
                $table->boolean('has_elevator')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_wardrobe')) {
                $table->boolean('has_wardrobe')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_surveillance')) {
                $table->boolean('has_surveillance')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_equipped_kitchen')) {
                $table->boolean('has_equipped_kitchen')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_air_conditioning')) {
                $table->boolean('has_air_conditioning')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_parking')) {
                $table->boolean('has_parking')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_security_door')) {
                $table->boolean('has_security_door')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_private_garden')) {
                $table->boolean('has_private_garden')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_yard')) {
                $table->boolean('has_yard')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_storage_room')) {
                $table->boolean('has_storage_room')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_smoke_outlet')) {
                $table->boolean('has_smoke_outlet')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_community_pool')) {
                $table->boolean('has_community_pool')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_private_pool')) {
                $table->boolean('has_private_pool')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_loading_area')) {
                $table->boolean('has_loading_area')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_24h_access')) {
                $table->boolean('has_24h_access')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_internal_transport')) {
                $table->boolean('has_internal_transport')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_alarm')) {
                $table->boolean('has_alarm')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_access_code')) {
                $table->boolean('has_access_code')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_free_parking')) {
                $table->boolean('has_free_parking')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_laundry')) {
                $table->boolean('has_laundry')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_community_area')) {
                $table->boolean('has_community_area')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_office_kitchen')) {
                $table->boolean('has_office_kitchen')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_jacuzzi')) {
                $table->boolean('has_jacuzzi')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_sauna')) {
                $table->boolean('has_sauna')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_tennis_court')) {
                $table->boolean('has_tennis_court')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_gym')) {
                $table->boolean('has_gym')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_sports_area')) {
                $table->boolean('has_sports_area')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_children_area')) {
                $table->boolean('has_children_area')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_home_automation')) {
                $table->boolean('has_home_automation')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_internet')) {
                $table->boolean('has_internet')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_suite_bathroom')) {
                $table->boolean('has_suite_bathroom')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_home_appliances')) {
                $table->boolean('has_home_appliances')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_oven')) {
                $table->boolean('has_oven')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_washing_machine')) {
                $table->boolean('has_washing_machine')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_microwave')) {
                $table->boolean('has_microwave')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_fridge')) {
                $table->boolean('has_fridge')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_tv')) {
                $table->boolean('has_tv')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_parquet')) {
                $table->boolean('has_parquet')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_stoneware')) {
                $table->boolean('has_stoneware')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'has_balcony')) {
                $table->boolean('has_balcony')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'pets_allowed')) {
                $table->boolean('pets_allowed')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'nearby_public_transport')) {
                $table->boolean('nearby_public_transport')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'land_area')) {
                $table->decimal('land_area', 8, 2)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inmuebles', function (Blueprint $table) {
            $table->dropColumn([
                'estado',
                'disponibilidad',
                'galeria',
                'otras_caracteristicas',
                'referencia_catastral',
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
                'land_area'
            ]);
        });
    }
};
