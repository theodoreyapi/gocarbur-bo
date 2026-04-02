<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->cascadeOnDelete();
            $table->enum('fuel_type', ['essence', 'gasoil', 'sans_plomb', 'super', 'gpl']);
            $table->decimal('price', 8, 2)->comment('Prix en FCFA par litre');
            $table->boolean('is_available')->default(true);
            $table->timestamp('updated_at_price')->nullable()->comment('Dernière mise à jour du prix');
            $table->timestamps();

            $table->unique(['station_id', 'fuel_type']);
            $table->index('fuel_type');
            $table->index('price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_prices');
    }
};
