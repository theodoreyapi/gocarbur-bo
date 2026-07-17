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
        Schema::table('fuel_prices', function (Blueprint $table) {
            $table->dropUnique(['fuel_type']);
            $table->unique(['station_id', 'fuel_type']);
        });

        Schema::table('station_services', function (Blueprint $table) {
            $table->dropUnique(['service']);
            $table->unique(['station_id', 'service']);
        });
    }

    public function down(): void
    {
        Schema::table('fuel_prices', function (Blueprint $table) {
            $table->dropUnique(['station_id', 'fuel_type']);
            $table->unique('fuel_type');
        });

        Schema::table('station_services', function (Blueprint $table) {
            $table->dropUnique(['station_id', 'service']);
            $table->unique('service');
        });
    }
};
