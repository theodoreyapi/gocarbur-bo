<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('document_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', [
                'expiration_assurance',
                'expiration_visite_technique',
                'expiration_permis',
                'vidange',
                'entretien',
                'controle_pneus',
                'controle_batterie',
                'revision',
                'personnalise'
            ]);
            $table->string('title');
            $table->text('notes')->nullable();
            $table->date('remind_at')->comment('Date du rappel');
            $table->unsignedInteger('remind_before_days')->default(15)->comment('Rappel X jours avant');
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->boolean('is_dismissed')->default(false)->comment('Rappel ignoré par l\'utilisateur');
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurrence', ['mensuel', 'trimestriel', 'annuel'])->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('remind_at');
            $table->index(['is_sent', 'remind_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
