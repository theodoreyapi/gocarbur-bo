<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->cascadeOnDelete();
            $table->enum('fuel_type', ['essence', 'gasoil', 'sans_plomb', 'super', 'gpl']);
            $table->decimal('old_price', 8, 2)->comment('Ancien prix en FCFA');
            $table->decimal('new_price', 8, 2)->comment('Nouveau prix en FCFA');
            $table->string('changed_by_type')->nullable()->comment('admin, station_owner');
            $table->unsignedBigInteger('changed_by_id')->nullable()->comment('ID de qui a changé');
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index(['station_id', 'fuel_type']);
            $table->index('changed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_price_history');
    }
};
