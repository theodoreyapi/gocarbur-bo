<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id('id_reminder')->primary();
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

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id_user_carbu')->on('users_carbur')->onDelete('cascade');
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->foreign('vehicle_id')->references('id_vehicule')->on('vehicles');
            $table->unsignedBigInteger('document_id')->nullable();
            $table->foreign('document_id')->references('id_doc')->on('documents');

            $table->index('remind_at');
            $table->index(['is_sent', 'remind_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropForeign(['user_id','vehicle_id','document_id']);
            $table->dropColum('user_id');
            $table->dropColum('vehicle_id');
            $table->dropColum('document_id');
        });
    }
};
