<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id('id_vehicule')->primary();
            $table->string('brand')->comment('Marque : Toyota, Honda...');              // Marque : Toyota, Honda...
            $table->string('model')->comment('Modèle : Corolla, Civic...');              // Modèle : Corolla, Civic...
            $table->year('year')->comment('2020');                 // Année fabrication
            $table->string('plate_number', 20)->nullable()->comment('Immatriculation');  // Immatriculation
            $table->enum('fuel_type', ['essence', 'gasoil', 'hybride', 'electrique'])->default('essence');
            $table->unsignedInteger('mileage')->nullable()->comment('Kilométrage actuel');
            $table->string('color')->nullable();
            $table->string('photo_url')->nullable();
            $table->boolean('is_primary')->default(false)->comment('Véhicule principal');
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id_user_carbu')->on('users_carbur')->onDelete('cascade');

            $table->index('plate_number');
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id('id_doc')->primary();
            $table->enum('type', [
                'permis_conduire',
                'assurance',
                'carte_grise',
                'visite_technique',
                'vignette',
                'autre'
            ]);
            $table->string('number')->nullable()->comment('Numéro du document');
            $table->date('issue_date')->nullable()->comment('Date de délivrance');
            $table->date('expiry_date')->nullable()->comment('Date d\'expiration');
            $table->string('file_url')->nullable()->comment('URL photo ou scan');
            $table->string('file_path')->nullable()->comment('Chemin stockage local');
            $table->enum('status', ['valid', 'expiring_soon', 'expired'])->default('valid');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedBigInteger('vehicle_id');
            $table->foreign('vehicle_id')->references('id_vehicule')->on('vehicles')->onDelete('cascade');

            $table->index('type');
            $table->index('expiry_date');
            $table->index('status');
        });

        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id('id_maint_log')->primary();
            $table->enum('type', [
                'vidange',
                'pneus',
                'batterie',
                'freins',
                'filtres',
                'climatisation',
                'courroie',
                'amortisseurs',
                'reparation',
                'revision',
                'autre'
            ]);
            $table->string('title')->comment('Ex: Vidange + filtre huile');
            $table->text('notes')->nullable();
            $table->decimal('cost', 10, 2)->nullable()->comment('Coût en FCFA');
            $table->date('done_at')->comment('Date de l\'intervention');
            $table->unsignedInteger('mileage_at_service')->nullable()->comment('Km au moment de l\'entretien');
            $table->unsignedInteger('next_service_mileage')->nullable()->comment('Prochain entretien à X km');
            $table->date('next_service_date')->nullable()->comment('Date prochain entretien');
            $table->string('garage_name')->nullable()->comment('Nom du garage prestataire');
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedBigInteger('vehicle_id');
            $table->foreign('vehicle_id')->references('id_vehicule')->on('vehicles')->onDelete('cascade');

            $table->index('done_at');
            $table->index('type');
        });

        Schema::create('fuel_logs', function (Blueprint $table) {
            $table->id('id_fuel_log')->primary();
            $table->enum('fuel_type', ['essence', 'gasoil', 'sans_plomb', 'super'])->default('essence');
            $table->decimal('liters', 8, 2)->comment('Nombre de litres');
            $table->decimal('price_per_liter', 8, 2)->comment('Prix par litre en FCFA');
            $table->decimal('total_cost', 10, 2)->comment('Coût total en FCFA');
            $table->unsignedInteger('mileage')->nullable()->comment('Km au moment du plein');
            $table->boolean('full_tank')->default(true)->comment('Plein complet');
            $table->text('notes')->nullable();
            $table->timestamp('filled_at')->comment('Date et heure du plein');
            $table->timestamps();

            $table->unsignedBigInteger('vehicle_id');
            $table->foreign('vehicle_id')->references('id_vehicule')->on('vehicles')->onDelete('cascade');
            $table->unsignedBigInteger('station_id')->nullable();
            $table->foreign('station_id')->references('id_station')->on('stations');

            $table->index('filled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColum('user_id');
        });

        Schema::dropIfExists('documents');
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropColum('vehicle_id');
        });

        Schema::dropIfExists('maintenance_logs');
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropColum('vehicle_id');
        });

        Schema::dropIfExists('fuel_logs');
        Schema::table('fuel_logs', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id', 'station_id']);
            $table->dropColum('vehicle_id');
            $table->dropColum('station_id');
        });
    }
};
