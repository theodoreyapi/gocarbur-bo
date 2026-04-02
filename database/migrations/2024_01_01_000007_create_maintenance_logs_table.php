<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
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

            $table->index('vehicle_id');
            $table->index('done_at');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};
