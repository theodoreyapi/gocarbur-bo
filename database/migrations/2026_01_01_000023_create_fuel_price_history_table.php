<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_price_history', function (Blueprint $table) {
            $table->id('id_fuel_price_histo')->primary();
            $table->enum('fuel_type', ['essence', 'gasoil', 'sans_plomb', 'super', 'gpl']);
            $table->decimal('old_price', 8, 2)->comment('Ancien prix en FCFA');
            $table->decimal('new_price', 8, 2)->comment('Nouveau prix en FCFA');
            $table->string('changed_by_type')->nullable()->comment('admin, station_owner');
            $table->unsignedBigInteger('changed_by_id')->nullable()->comment('ID de qui a changé');
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->unsignedBigInteger('station_id');
            $table->foreign('station_id')->references('id_station')->on('stations')->onDelete('cascade');

            $table->index('changed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_price_history');
        Schema::table('fuel_price_history', function (Blueprint $table) {
            $table->dropForeign(['station_id']);
            $table->dropColum('station_id');
        });
    }
};
