<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('id_notification')->primary();
            $table->enum('type', [
                'document_expiry',
                'fuel_alert',
                'promotion',
                'reminder',
                'system',
                'conseil',
                'broadcast'
            ]);
            $table->string('title');
            $table->text('body');
            $table->string('icon')->nullable()->comment('Icône ou image de la notif');
            $table->json('data')->nullable()->comment('Données extras: station_id, article_id...');
            $table->string('action_url')->nullable()->comment('Lien deep link dans l\'app');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_push_sent')->default(false)->comment('Push Firebase envoyé');
            $table->timestamp('push_sent_at')->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id_user_carbu')->on('users_carbur')->onDelete('cascade');

            $table->index('is_read');
            $table->index('type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColum('user_id');
        });
    }
};
