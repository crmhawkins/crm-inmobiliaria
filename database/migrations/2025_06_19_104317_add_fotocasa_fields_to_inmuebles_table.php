<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inmuebles', function (Blueprint $table) {
            if (!Schema::hasColumn('inmuebles', 'external_id')) {
                $table->string('external_id')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'agency_reference')) {
                $table->string('agency_reference')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'type_id')) {
                $table->unsignedBigInteger('type_id')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'subtype_id')) {
                $table->unsignedBigInteger('subtype_id')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'contact_type_id')) {
                $table->unsignedBigInteger('contact_type_id')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'zip_code')) {
                $table->string('zip_code')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'floor_id')) {
                $table->unsignedBigInteger('floor_id')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'x')) {
                $table->decimal('x', 12, 8)->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'y')) {
                $table->decimal('y', 12, 8)->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'visibility_mode_id')) {
                $table->unsignedBigInteger('visibility_mode_id')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'street')) {
                $table->string('street')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'number')) {
                $table->string('number')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'features')) {
                $table->json('features')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'contact_info')) {
                $table->json('contact_info')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'transactions')) {
                $table->json('transactions')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'publications')) {
                $table->json('publications')->nullable();
            }
            if (!Schema::hasColumn('inmuebles', 'documentos')) {
                $table->json('documentos')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('inmuebles', function (Blueprint $table) {
            $table->dropColumn([
                'external_id',
                'agency_reference',
                'type_id',
                'subtype_id',
                'contact_type_id',
                'zip_code',
                'floor_id',
                'x',
                'y',
                'visibility_mode_id',
                'street',
                'number',
                'features',
                'contact_info',
                'transactions',
                'publications',
                'documentos'
            ]);
        });
    }
};
