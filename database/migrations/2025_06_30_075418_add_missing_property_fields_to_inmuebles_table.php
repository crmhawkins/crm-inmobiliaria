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
    public function up(): void
    {
        Schema::table('inmuebles', function (Blueprint $table) {
            // Campos de Fotocasa que faltan
            $fields = [
                'building_subtype_id' => 'unsignedBigInteger',
                'transaction_type_id' => 'unsignedBigInteger',
                'visibility_mode_id' => 'unsignedBigInteger',
                'floor_id' => 'unsignedBigInteger',
                'orientation_id' => 'unsignedBigInteger',
                'heating_type_id' => 'unsignedBigInteger',
                'hot_water_type_id' => 'unsignedBigInteger',
                'consumption_efficiency_scale' => 'unsignedTinyInteger',
                'emissions_efficiency_scale' => 'unsignedTinyInteger',
                'consumption_efficiency_value' => 'decimal:8,2',
                'emissions_efficiency_value' => 'decimal:8,2',
                'energy_certificate_status' => 'string',
                'conservation_status' => 'string',
                'year_built' => 'unsignedInteger',

                // Campos booleanos principales
                'furnished' => 'boolean',
                'has_elevator' => 'boolean',
                'has_terrace' => 'boolean',
                'has_balcony' => 'boolean',
                'has_parking' => 'boolean',
                'has_air_conditioning' => 'boolean',
                'has_heating' => 'boolean',
                'has_security_door' => 'boolean',
                'has_equipped_kitchen' => 'boolean',
                'has_wardrobe' => 'boolean',
                'has_storage_room' => 'boolean',
                'pets_allowed' => 'boolean',

                // Campos adicionales
                'terrace_surface' => 'decimal:8,2',
                'has_private_garden' => 'boolean',
                'has_yard' => 'boolean',
                'has_smoke_outlet' => 'boolean',
                'has_community_pool' => 'boolean',
                'has_private_pool' => 'boolean',
                'has_loading_area' => 'boolean',
                'has_24h_access' => 'boolean',
                'has_internal_transport' => 'boolean',
                'has_alarm' => 'boolean',
                'has_access_code' => 'boolean',
                'has_free_parking' => 'boolean',
                'has_laundry' => 'boolean',
                'has_community_area' => 'boolean',
                'has_office_kitchen' => 'boolean',
                'has_jacuzzi' => 'boolean',
                'has_sauna' => 'boolean',
                'has_tennis_court' => 'boolean',
                'has_gym' => 'boolean',
                'has_sports_area' => 'boolean',
                'has_children_area' => 'boolean',
                'has_home_automation' => 'boolean',
                'has_internet' => 'boolean',
                'has_suite_bathroom' => 'boolean',
                'has_home_appliances' => 'boolean',
                'has_oven' => 'boolean',
                'has_washing_machine' => 'boolean',
                'has_microwave' => 'boolean',
                'has_fridge' => 'boolean',
                'has_tv' => 'boolean',
                'has_parquet' => 'boolean',
                'has_stoneware' => 'boolean',
                'nearby_public_transport' => 'boolean',
                'land_area' => 'decimal:10,2',
            ];

            foreach ($fields as $name => $type) {
                if (!Schema::hasColumn('inmuebles', $name)) {
                    [$baseType, $args] = array_pad(explode(':', $type), 2, null);
                    if ($args) {
                        [$precision, $scale] = explode(',', $args);
                        $column = $table->$baseType($name, (int) $precision, (int) $scale);
                    } else {
                        $column = $table->$baseType($name);
                    }
                    $column->nullable();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('inmuebles', function (Blueprint $table) {
            $table->dropColumn([
                'building_subtype_id',
                'transaction_type_id',
                'visibility_mode_id',
                'floor_id',
                'orientation_id',
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
                'has_terrace',
                'has_balcony',
                'has_parking',
                'has_air_conditioning',
                'has_heating',
                'has_security_door',
                'has_equipped_kitchen',
                'has_wardrobe',
                'has_storage_room',
                'pets_allowed',
                'terrace_surface',
                'has_private_garden',
                'has_yard',
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
                'nearby_public_transport',
                'land_area',
            ]);
        });
    }
};
