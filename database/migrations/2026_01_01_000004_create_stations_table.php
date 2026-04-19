<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('station_owners', function (Blueprint $table) {
            $table->id('id_station_owner')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->string('company_name')->nullable()->comment('Nom de la société');
            $table->string('rccm')->nullable()->comment('Numéro RCCM Côte d\'Ivoire');
            $table->enum('status', ['pending', 'approved', 'suspended', 'rejected'])->default('pending');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('email');
        });

        Schema::create('stations', function (Blueprint $table) {
            $table->id('id_station')->primary();
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('country', 3)->default('CI');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('phone', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('logo_url')->nullable();
            $table->json('photos')->nullable()->comment('Galerie photos JSON array');
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->boolean('is_open_24h')->default(false);
            $table->boolean('is_verified')->default(false)->comment('Badge vérifié admin');
            $table->enum('subscription_type', ['free', 'pro', 'premium'])->default('free');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedBigInteger('owner_id');
            $table->foreign('owner_id')->references('id_station_owner')->on('station_owners')->onDelete('cascade');

            $table->index(['latitude', 'longitude']);
            $table->index('subscription_type');
            $table->index('is_active');
            $table->index('city');
        });

        Schema::create('station_owner_station', function (Blueprint $table) {
            $table->id('id_stat_owner_stat')->primary();

            $table->unsignedBigInteger('owner_id');
            $table->foreign('owner_id')->references('id_station_owner')->on('station_owners')->onDelete('cascade');

            $table->unsignedBigInteger('station_id');
            $table->foreign('station_id')->references('id_station')->on('stations')->onDelete('cascade');

            $table->enum('role', ['owner', 'manager', 'employee'])->default('owner');
            $table->timestamps();
        });

        Schema::create('fuel_prices', function (Blueprint $table) {
            $table->id('id_fuel_price')->primary();
            $table->enum('fuel_type', ['essence', 'gasoil', 'sans_plomb', 'super', 'gpl']);
            $table->decimal('price', 8, 2)->comment('Prix en FCFA par litre');
            $table->boolean('is_available')->default(true);
            $table->timestamp('updated_at_price')->nullable()->comment('Dernière mise à jour du prix');
            $table->timestamps();

            $table->unsignedBigInteger('station_id');
            $table->foreign('station_id')->references('id_station')->on('stations')->onDelete('cascade');

            $table->unique('fuel_type');
            $table->index('fuel_type');
            $table->index('price');
        });

        Schema::create('station_services', function (Blueprint $table) {
            $table->id('id_sta_service')->primary();
            $table->enum('service', [
                'lavage_auto',
                'gonflage_pneus',
                'boutique',
                'restaurant',
                'toilettes',
                'wifi',
                'atm',
                'parking',
                'gonflage_gratuit',
                'huile_moteur',
                'reparation_rapide'
            ]);

            $table->unsignedBigInteger('station_id');
            $table->foreign('station_id')->references('id_station')->on('stations')->onDelete('cascade');

            $table->timestamps();

            $table->unique('service');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_owners');

        Schema::dropIfExists('stations');
        Schema::table('stations', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropColum('owner_id');
        });

        Schema::dropIfExists('station_owner_station');
        Schema::table('station_owner_station', function (Blueprint $table) {
            $table->dropForeign(['owner_id', 'station_id']);
            $table->dropColum('owner_id');
            $table->dropColum('station_id');
        });

        Schema::dropIfExists('fuel_prices');
        Schema::table('fuel_prices', function (Blueprint $table) {
            $table->dropForeign(['station_id']);
            $table->dropColum('station_id');
        });

        Schema::dropIfExists('station_services');
        Schema::table('station_services', function (Blueprint $table) {
            $table->dropForeign(['station_id']);
            $table->dropColum('station_id');
        });
    }
};
