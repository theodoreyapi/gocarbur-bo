<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->enum('type', [
                'permis_conduire',
                'assurance',
                'carte_grise',
                'visite_technique',
                'vignette',
                'autre'
            ]);
            $table->string('number')->nullable()->comment('Numéro du document');
            $table->date('issue_date')->nullable()->comment('Date de délivrance');
            $table->date('expiry_date')->nullable()->comment('Date d\'expiration');
            $table->string('file_url')->nullable()->comment('URL photo ou scan');
            $table->string('file_path')->nullable()->comment('Chemin stockage local');
            $table->enum('status', ['valid', 'expiring_soon', 'expired'])->default('valid');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['vehicle_id', 'type']);
            $table->index('expiry_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
