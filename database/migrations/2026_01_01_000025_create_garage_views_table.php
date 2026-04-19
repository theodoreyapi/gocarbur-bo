<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garage_views', function (Blueprint $table) {
            $table->id('id_gara_view')->primary();
            $table->string('ip_address', 45)->nullable();
            $table->enum('action', ['view_profile', 'view_map', 'call', 'whatsapp', 'itinerary'])->default('view_profile');
            $table->timestamp('viewed_at');

            $table->unsignedBigInteger('garage_id');
            $table->foreign('garage_id')->references('id_garage')->on('garages')->onDelete('cascade');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id_user_carbu')->on('users_carbur');

            $table->index('viewed_at');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garage_views');
        Schema::table('garage_views', function (Blueprint $table) {
            $table->dropForeign(['user_id','garage_id']);
            $table->dropColum('user_id');
            $table->dropColum('garage_id');
        });
    }
};
