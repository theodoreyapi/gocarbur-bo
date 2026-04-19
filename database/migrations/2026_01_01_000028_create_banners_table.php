<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id('id_banner')->primary();
            $table->string('title');
            $table->string('image_url');
            $table->string('action_url')->nullable()->comment('Lien au clic: deeplink ou URL externe');
            $table->enum('position', [
                'home_top',
                'home_middle',
                'map_bottom',
                'stations_list',
                'garages_list',
                'articles_list',
                'splash'
            ])->comment('Emplacement dans l\'app');
            $table->enum('target_type', ['all', 'free_users', 'premium_users', 'city'])->default('all');
            $table->string('target_city')->nullable()->comment('Cibler une ville spécifique');
            $table->string('advertiser_name')->nullable();
            $table->decimal('price_paid', 10, 2)->nullable()->comment('Montant pub payé en FCFA');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('impressions_count')->default(0);
            $table->unsignedInteger('clicks_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('position');
            $table->index(['starts_at', 'ends_at']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
