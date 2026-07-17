<?php

namespace App\Http\Controllers;

use App\Models\HealthProfile;
use App\Models\PaymentTransaction;
use App\Models\ProfileSubscription;
use App\Models\Rechargements;
use App\Models\Subscription;
use App\Models\UsersPharma;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentWaveController extends Controller
{
    /**
     * ✅ PAGE SUCCESS
     */
    public function success($id)
    {

        // ✅ Récupère l'objet complet, puis accède à l'attribut
        $payment = Subscription::where('id_subcrip', $id)->first();

        if (!$payment) {
            return view('payment.error', ['message' => 'Souscription introuvable']);
        }

        $checkoutId = $payment->payment_transaction_id; // ✅ string correcte

        // Vérification réelle chez Wave (source de vérité)
        $response = Http::withHeaders([
            'Authorization' => 'Bearer wave_ci_prod_tIc5B0OlAxjucp29W83a2YLvua7Z7FOTmAFYtQlONucpqcNHU0TklALECuBP-nf5HL8HkGgopw0UzPFz2aXld43qhMcAwXINng',
            'Content-Type'  => 'application/json',
        ])->get("https://api.wave.com/v1/checkout/sessions/$checkoutId");

        if (!$response->successful()) {
            return view('payment.error', [
                'message' => 'Impossible de vérifier le paiement',
            ]);
        }

        $session = $response->json();

        if ($session['payment_status'] !== 'succeeded') {
            return view('payment.error', [
                'message' => 'Paiement non confirmé',
            ]);
        }

        // ── Traitement idempotent (eviter double crédit) ──────────────────
        if ($payment->status !== 'active') {
            DB::transaction(function () use ($payment, $session) {

                // Mettre à jour l'abonnement
                $payment->update([
                    'status'         => 'active',
                    'payment_reference' => $session['transaction_id'] ?? null,
                    'updated_at'     => Carbon::now(),
                    'paid_at'     => Carbon::now(),
                ]);

                // Mettre à jour la transaction
                DB::table('payment_transactions')
                    ->where('subscription_id', $payment->id_subcrip)
                    ->update([
                        'operator_reference' => $session['transaction_id'] ?? null,
                        'status' => 'success',
                    ]);

                DB::table('users_carbur')
                    ->where('id_user_carbu', $payment->subscribable_id)
                    ->update([
                        'subscription_type'       => 'premium',
                        'subscription_expires_at' => Carbon::now(),
                        'updated_at'              => now(),
                    ]);
            });
        }

        return view('payment.success', [
            'amount'    => $session['amount'],
            'reference' => $session['transaction_id'] ?? 'N/A',
            'business'  => $session['business_name'] ?? 'GoCarbu',
        ]);
    }

    /**
     * ❌ PAGE ERROR
     */
    public function error($id)
    {
        return view('payment.error', [
            'amount' => 0,
            'message' => 'Paiement annulé ou échoué',
            'rechargement_id' => $id,
        ]);
    }


    /**
     * ✅ PAGE SUCCESS
     */
    public function successpro($id)
    {

        // ✅ Récupère l'objet complet, puis accède à l'attribut
        $payment = Subscription::where('id_subcrip', $id)->first();

        if (!$payment) {
            return view('payment.error', ['message' => 'Souscription introuvable']);
        }

        $checkoutId = $payment->payment_transaction_id; // ✅ string correcte

        // Vérification réelle chez Wave (source de vérité)
        $response = Http::withHeaders([
            'Authorization' => 'Bearer wave_ci_prod_tIc5B0OlAxjucp29W83a2YLvua7Z7FOTmAFYtQlONucpqcNHU0TklALECuBP-nf5HL8HkGgopw0UzPFz2aXld43qhMcAwXINng',
            'Content-Type'  => 'application/json',
        ])->get("https://api.wave.com/v1/checkout/sessions/$checkoutId");

        if (!$response->successful()) {
            return view('payment.error', [
                'message' => 'Impossible de vérifier le paiement',
            ]);
        }

        $session = $response->json();

        if ($session['payment_status'] !== 'succeeded') {
            return view('payment.error', [
                'message' => 'Paiement non confirmé',
            ]);
        }

        // ── Traitement idempotent (eviter double crédit) ──────────────────
        if ($payment->status !== 'active') {
            DB::transaction(function () use ($payment, $session) {

                // Mettre à jour l'abonnement
                $payment->update([
                    'status'         => 'active',
                    'payment_reference' => $session['transaction_id'] ?? null,
                    'updated_at'     => Carbon::now(),
                    'paid_at'     => Carbon::now(),
                ]);

                // Mettre à jour la transaction
                DB::table('payment_transactions')
                    ->where('subscription_id', $payment->id_subcrip)
                    ->update([
                        'operator_reference' => $session['transaction_id'] ?? null,
                        'status' => 'success',
                    ]);
            });
        }

        return view('payment.success', [
            'amount'    => $session['amount'],
            'reference' => $session['transaction_id'] ?? 'N/A',
            'business'  => $session['business_name'] ?? 'GoCarbu',
        ]);
    }

    /**
     * ❌ PAGE ERROR
     */
    public function errorpro($id)
    {
        return view('payment.error', [
            'amount' => 0,
            'message' => 'Paiement annulé ou échoué',
            'rechargement_id' => $id,
        ]);
    }
}
