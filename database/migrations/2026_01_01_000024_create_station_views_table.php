<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('station_views', function (Blueprint $table) {
            $table->id('id_sta_view')->primary();
            $table->string('ip_address', 45)->nullable();
            $table->enum('action', ['view_profile', 'view_map', 'call', 'whatsapp', 'itinerary'])->default('view_profile');
            $table->timestamp('viewed_at');

            $table->unsignedBigInteger('station_id');
            $table->foreign('station_id')->references('id_station')->on('stations')->onDelete('cascade');
            
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id_user_carbu')->on('users_carbur');

            $table->index('viewed_at');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_views');
        Schema::table('station_views', function (Blueprint $table) {
            $table->dropForeign(['user_id','station_id']);
            $table->dropColum('user_id');
            $table->dropColum('station_id');
        });
    }
};
