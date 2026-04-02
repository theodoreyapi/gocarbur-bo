<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garage_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garage_id')->constrained()->cascadeOnDelete();
            $table->enum('service', [
                'vidange',
                'freins',
                'pneus',
                'batterie',
                'climatisation',
                'electricite',
                'carrosserie',
                'vitrage',
                'courroie_distribution',
                'amortisseurs',
                'echappement',
                'revision_complete',
                'diagnostic_electronique',
                'depannage_route',
                'remorquage',
                'lavage_interieur',
                'lavage_exterieur',
                'polissage'
            ]);
            $table->string('price_range')->nullable()->comment('Ex: 5000 - 15000 FCFA');
            $table->timestamps();

            $table->unique(['garage_id', 'service']);
            $table->index('garage_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garage_services');
    }
};
