<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('station_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('fuel_type', ['essence', 'gasoil', 'sans_plomb', 'super'])->default('essence');
            $table->decimal('liters', 8, 2)->comment('Nombre de litres');
            $table->decimal('price_per_liter', 8, 2)->comment('Prix par litre en FCFA');
            $table->decimal('total_cost', 10, 2)->comment('Coût total en FCFA');
            $table->unsignedInteger('mileage')->nullable()->comment('Km au moment du plein');
            $table->boolean('full_tank')->default(true)->comment('Plein complet');
            $table->text('notes')->nullable();
            $table->timestamp('filled_at')->comment('Date et heure du plein');
            $table->timestamps();

            $table->index('vehicle_id');
            $table->index('filled_at');
            $table->index(['vehicle_id', 'filled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_logs');
    }
};
