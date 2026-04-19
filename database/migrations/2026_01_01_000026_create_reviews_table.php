<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id('id_review')->primary();
            // Polymorphique : station ou garage
            $table->morphs('reviewable');   // reviewable_type + reviewable_id
            $table->unsignedTinyInteger('rating')->comment('Note de 1 à 5');
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false)->comment('Modération admin');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id_user_carbu')->on('users_carbur')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColum('user_id');
        });
    }
};
