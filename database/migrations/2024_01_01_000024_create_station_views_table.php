<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('station_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->enum('action', ['view_profile', 'view_map', 'call', 'whatsapp', 'itinerary'])->default('view_profile');
            $table->timestamp('viewed_at');

            $table->index(['station_id', 'viewed_at']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_views');
    }
};
