<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('station_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->cascadeOnDelete();
            $table->enum('service', [
                'lavage_auto',
                'gonflage_pneus',
                'boutique',
                'restaurant',
                'toilettes',
                'wifi',
                'atm',
                'parking',
                'gonflage_gratuit',
                'huile_moteur',
                'reparation_rapide'
            ]);
            $table->timestamps();

            $table->unique(['station_id', 'service']);
            $table->index('station_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_services');
    }
};
