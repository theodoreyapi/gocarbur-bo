<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('brand');              // Marque : Toyota, Honda...
            $table->string('model');              // Modèle : Corolla, Civic...
            $table->year('year');                 // Année fabrication
            $table->string('plate_number', 20)->nullable();  // Immatriculation
            $table->enum('fuel_type', ['essence', 'gasoil', 'hybride', 'electrique'])->default('essence');
            $table->unsignedInteger('mileage')->nullable()->comment('Kilométrage actuel');
            $table->string('color')->nullable();
            $table->string('photo_url')->nullable();
            $table->boolean('is_primary')->default(false)->comment('Véhicule principal');
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('plate_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
