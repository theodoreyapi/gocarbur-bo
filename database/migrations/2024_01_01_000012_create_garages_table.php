<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', [
                'garage_general',
                'centre_vidange',
                'lavage_auto',
                'pneus',
                'batterie',
                'climatisation',
                'electricite_auto',
                'depannage',
                'carrosserie',
                'vitrage'
            ]);
            $table->string('address');
            $table->string('city');
            $table->string('country', 3)->default('CI');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('phone', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('logo_url')->nullable();
            $table->json('photos')->nullable();
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->boolean('is_open_24h')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->enum('subscription_type', ['free', 'pro', 'premium'])->default('free');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->decimal('rating', 3, 2)->nullable()->default(0.00);
            $table->unsignedInteger('rating_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['latitude', 'longitude']);
            $table->index('type');
            $table->index('subscription_type');
            $table->index('is_active');
            $table->index('city');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garages');
    }
};
