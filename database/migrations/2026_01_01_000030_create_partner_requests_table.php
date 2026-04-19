<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_requests', function (Blueprint $table) {
            $table->id('id_demande')->primary();
            $table->enum('type', ['station', 'garage']);
            $table->string('business_name')->comment('Nom de l\'établissement');
            $table->string('contact_name')->comment('Nom du responsable');
            $table->string('contact_phone', 20);
            $table->string('contact_email')->nullable();
            $table->string('address');
            $table->string('city');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('message')->nullable()->comment('Message libre du demandeur');
            $table->enum('status', ['pending', 'contacted', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();

            $table->unsignedBigInteger('admin_id')->comment('Admin qui a traité');
            $table->foreign('admin_id')->references('id')->on('users');

            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_requests');
        Schema::table('partner_requests', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColum('admin_id');
        });
    }
};
