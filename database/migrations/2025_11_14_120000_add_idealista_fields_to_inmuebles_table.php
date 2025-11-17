<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inmuebles', function (Blueprint $table) {
            $table->unsignedBigInteger('idealista_property_id')
                ->nullable()
                ->after('external_id')
                ->unique();

            $table->string('idealista_code')
                ->nullable()
                ->after('idealista_property_id');

            $table->json('idealista_payload')
                ->nullable()
                ->after('idealista_code');

            $table->timestamp('idealista_synced_at')
                ->nullable()
                ->after('idealista_payload');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inmuebles', function (Blueprint $table) {
            $table->dropColumn([
                'idealista_property_id',
                'idealista_code',
                'idealista_payload',
                'idealista_synced_at',
            ]);
        });
    }
};

