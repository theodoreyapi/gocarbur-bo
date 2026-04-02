<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('station_owner_station', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_owner_id')->constrained('station_owners')->cascadeOnDelete();
            $table->foreignId('station_id')->constrained('stations')->cascadeOnDelete();
            $table->enum('role', ['owner', 'manager', 'employee'])->default('owner');
            $table->timestamps();

            $table->unique(['station_owner_id', 'station_id']);
            $table->index('station_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_owner_station');
    }
};
