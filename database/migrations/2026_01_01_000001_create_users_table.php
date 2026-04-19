<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users_carbur', function (Blueprint $table) {
            $table->id('id_user_carbu')->primary();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone', 20)->unique();
            $table->string('city')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('password');
            $table->string('token')->nullable();
            $table->enum('subscription_type', ['free', 'premium'])->default('free');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->string('fcm_token')->nullable()->comment('Token Firebase pour push notifications');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('phone');
            $table->index('subscription_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_carbur');
    }
};
