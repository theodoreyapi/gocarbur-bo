<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            // Polymorphique : user, station ou garage
            $table->morphs('subscribable');  // subscribable_type + subscribable_id
            $table->enum('plan', [
                // Plans utilisateurs
                'user_free',
                'user_premium',
                // Plans professionnels stations
                'station_free',
                'station_pro',
                'station_premium',
                // Plans professionnels garages
                'garage_free',
                'garage_pro',
                'garage_premium',
            ]);
            $table->decimal('amount', 10, 2)->comment('Montant payé en FCFA');
            $table->enum('billing_cycle', ['mensuel', 'trimestriel', 'annuel'])->default('mensuel');
            $table->date('starts_at');
            $table->date('expires_at');
            $table->enum('status', ['active', 'expired', 'cancelled', 'pending', 'failed'])->default('pending');
            // Paiement
            $table->enum('payment_method', ['orange_money', 'mtn_money', 'moov_money', 'cinetpay', 'wave', 'especes'])->nullable();
            $table->string('payment_reference')->nullable()->unique()->comment('Référence paiement opérateur');
            $table->string('payment_transaction_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['subscribable_type', 'subscribable_id']);
            $table->index('status');
            $table->index('expires_at');
            $table->index('plan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
