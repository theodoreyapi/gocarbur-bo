<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Polymorphique : station ou garage
            $table->morphs('favoriteable');  // favoriteable_type + favoriteable_id
            $table->timestamps();

            $table->unique(['user_id', 'favoriteable_type', 'favoriteable_id']);
            $table->index('user_id');
            $table->index(['favoriteable_type', 'favoriteable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
