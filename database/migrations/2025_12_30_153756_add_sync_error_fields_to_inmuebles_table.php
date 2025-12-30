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
            $table->text('idealista_sync_error')->nullable()->after('idealista_synced_at');
            $table->timestamp('idealista_last_sync_error_at')->nullable()->after('idealista_sync_error');
            $table->text('fotocasa_sync_error')->nullable()->after('external_id');
            $table->timestamp('fotocasa_last_sync_error_at')->nullable()->after('fotocasa_sync_error');
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
                'idealista_sync_error',
                'idealista_last_sync_error_at',
                'fotocasa_sync_error',
                'fotocasa_last_sync_error_at',
            ]);
        });
    }
};
