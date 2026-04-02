<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            // Polymorphique : peut appartenir à une station ou un garage
            $table->morphs('promotable');   // promotable_type + promotable_id
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->enum('type', ['discount', 'offre_speciale', 'service_gratuit', 'cadeau', 'autre'])->default('offre_speciale');
            $table->decimal('discount_percent', 5, 2)->nullable()->comment('Réduction en %');
            $table->decimal('discount_amount', 10, 2)->nullable()->comment('Réduction fixe en FCFA');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->boolean('send_push_notification')->default(false)->comment('Envoyer notif aux users proches');
            $table->unsignedInteger('notification_radius_km')->default(5)->comment('Rayon en km pour la notif');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['promotable_type', 'promotable_id']);
            $table->index(['starts_at', 'ends_at']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
