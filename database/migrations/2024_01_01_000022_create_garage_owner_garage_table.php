<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garage_owner_garage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garage_owner_id')->constrained('garage_owners')->cascadeOnDelete();
            $table->foreignId('garage_id')->constrained('garages')->cascadeOnDelete();
            $table->enum('role', ['owner', 'manager', 'employee'])->default('owner');
            $table->timestamps();

            $table->unique(['garage_owner_id', 'garage_id']);
            $table->index('garage_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garage_owner_garage');
    }
};
