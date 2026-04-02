<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand')->nullable()->comment('Ex: Total, Shell, Petro Ivoire');
            $table->string('address');
            $table->string('city');
            $table->string('country', 3)->default('CI');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('phone', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('logo_url')->nullable();
            $table->json('photos')->nullable()->comment('Galerie photos JSON array');
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->boolean('is_open_24h')->default(false);
            $table->boolean('is_verified')->default(false)->comment('Badge vérifié admin');
            $table->enum('subscription_type', ['free', 'pro', 'premium'])->default('free');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['latitude', 'longitude']);
            $table->index('subscription_type');
            $table->index('is_active');
            $table->index('city');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};
