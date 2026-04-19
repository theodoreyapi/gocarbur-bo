<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id('id_pay_transac')->primary();
            $table->string('reference')->unique()->comment('Référence interne unique');
            // Polymorphique : qui paie (user, station_owner, garage_owner)
            $table->morphs('payer');

            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->foreign('subscription_id')->references('id_subcrip')->on('subscriptions');

            $table->decimal('amount', 10, 2)->comment('Montant en FCFA');
            $table->enum('currency', ['XOF', 'EUR'])->default('XOF');
            $table->enum('payment_method', [
                'orange_money',
                'mtn_money',
                'moov_money',
                'wave',
                'cinetpay',
                'especes',
                'virement'
            ]);
            $table->enum('status', ['pending', 'success', 'failed', 'refunded', 'cancelled'])->default('pending');
            $table->string('operator_reference')->nullable()->comment('Référence de l\'opérateur');
            $table->string('operator_transaction_id')->nullable();
            $table->json('operator_response')->nullable()->comment('Réponse brute opérateur');
            $table->string('phone_payer', 20)->nullable()->comment('Numéro mobile ayant payé');
            $table->text('failure_reason')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
            $table->dropColum('subscription_id');
        });
    }
};
