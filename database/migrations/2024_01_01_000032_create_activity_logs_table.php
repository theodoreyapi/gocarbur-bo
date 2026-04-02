<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            // Qui a fait l'action (admin, user, station_owner, garage_owner)
            $table->morphs('causer');
            // Sur quoi (station, garage, user, article...)
            $table->nullableMorphs('subject');
            $table->string('action')->comment('Ex: created, updated, deleted, verified, banned');
            $table->text('description')->nullable();
            $table->json('old_values')->nullable()->comment('Valeurs avant modification');
            $table->json('new_values')->nullable()->comment('Valeurs après modification');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['causer_type', 'causer_id']);
            $table->index(['subject_type', 'subject_id']);
            $table->index('action');
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
