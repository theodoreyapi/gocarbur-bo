<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->id('id_app_version')->primary();
            $table->string('platform')->comment('android ou ios');
            $table->string('version', 20)->comment('Ex: 1.2.3');
            $table->unsignedInteger('build_number');
            $table->enum('force_update', ['none', 'optional', 'required'])->default('none');
            $table->string('store_url')->nullable()->comment('Lien Play Store ou App Store');
            $table->text('changelog')->nullable()->comment('Notes de version');
            $table->boolean('is_current')->default(false);
            $table->date('released_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_versions');
    }
};
