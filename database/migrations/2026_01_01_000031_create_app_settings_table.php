<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id('id_app_setting')->primary();
            $table->string('key')->unique()->comment('Clé de configuration');
            $table->text('value')->nullable();
            $table->enum('type', ['string', 'integer', 'boolean', 'json', 'decimal'])->default('string');
            $table->string('group')->default('general')->comment('Groupe: general, pricing, notifications...');
            $table->string('label')->comment('Label lisible pour l\'admin');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false)->comment('Accessible via API publique');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
