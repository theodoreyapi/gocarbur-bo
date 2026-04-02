<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable()->comment('Résumé court');
            $table->longText('content');
            $table->string('cover_image_url')->nullable();
            $table->enum('category', [
                'entretien_auto',
                'economie_carburant',
                'conduite_securite',
                'documents_admin',
                'astuces_mecaniques',
                'videos_conseils',
                'actualites',
                'legislation'
            ]);
            $table->boolean('is_sponsored')->default(false);
            $table->string('sponsor_name')->nullable();
            $table->string('sponsor_logo_url')->nullable();
            $table->string('sponsor_url')->nullable()->comment('Lien sponsor');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('read_time_minutes')->nullable()->comment('Temps de lecture estimé');
            $table->json('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('category');
            $table->index('is_published');
            $table->index('is_sponsored');
            $table->index('published_at');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
