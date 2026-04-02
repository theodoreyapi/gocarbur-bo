<?php

namespace App\Services;

use App\Models\PaymentTransaction;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Initier un paiement selon la méthode choisie
     */
    public function initiate(array $params): array
    {
        return match ($params['method']) {
            'cinetpay'    => $this->initiateCinetPay($params),
            'orange_money'=> $this->initiateOrangeMoney($params),
            'mtn_money'   => $this->initiateMtnMomo($params),
            'wave'        => $this->initiateWave($params),
            'moov_money'  => $this->initiateMoovMoney($params),
            default       => ['success' => false, 'message' => 'Méthode de paiement non supportée.'],
        };
    }

    // ── CinetPay ──────────────────────────────────────────────────────────

    private function initiateCinetPay(array $params): array
    {
        try {
            $response = Http::post('https://api-checkout.cinetpay.com/v2/payment', [
                'apikey'          => config('services.cinetpay.api_key'),
                'site_id'         => config('services.cinetpay.site_id'),
                'transaction_id'  => $params['reference'],
                'amount'          => $params['amount'],
                'currency'        => 'XOF',
                'description'     => $params['description'],
                'notify_url'      => config('app.url') . '/api/v1/webhooks/cinetpay',
                'return_url'      => config('app.url') . '/paiement/succes',
                'customer_phone_number' => $params['phone'],
                'channels'        => 'ALL',
                'lang'            => 'fr',
            ]);

            $body = $response->json();

            if ($response->successful() && isset($body['data']['payment_url'])) {
                return [
                    'success'     => true,
                    'payment_url' => $body['data']['payment_url'],
                    'token'       => $body['data']['payment_token'] ?? null,
                ];
            }

            return ['success' => false, 'message' => $body['message'] ?? 'Erreur CinetPay.'];

        } catch (\Throwable $e) {
            Log::error('CinetPay initiate error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Service de paiement indisponible.'];
        }
    }

    // ── Orange Money CI ───────────────────────────────────────────────────

    private function initiateOrangeMoney(array $params): array
    {
        try {
            // Étape 1 : Obtenir le token d'accès
            $tokenResponse = Http::withBasicAuth(
                config('services.orange_money.client_id'),
                config('services.orange_money.client_secret')
            )->asForm()->post('https://api.orange.com/oauth/v3/token', [
                'grant_type' => 'client_credentials',
            ]);

            $accessToken = $tokenResponse->json('access_token');
            if (!$accessToken) {
                return ['success' => false, 'message' => 'Authentification Orange Money échouée.'];
            }

            // Étape 2 : Initier le paiement
            $response = Http::withToken($accessToken)
                ->post('https://api.orange.com/orange-money-webpay/ci/v1/webpayment', [
                    'merchant_key'   => config('services.orange_money.merchant_key'),
                    'currency'       => 'OUV',
                    'order_id'       => $params['reference'],
                    'amount'         => $params['amount'],
                    'return_url'     => config('app.url') . '/paiement/succes',
                    'cancel_url'     => config('app.url') . '/paiement/annule',
                    'notif_url'      => config('app.url') . '/api/v1/webhooks/orange-money',
                    'lang'           => 'fr',
                    'reference'      => $params['reference'],
                ]);

            $body = $response->json();

            if (isset($body['payment_url'])) {
                return [
                    'success'     => true,
                    'payment_url' => $body['payment_url'],
                    'pay_token'   => $body['pay_token'] ?? null,
                ];
            }

            return ['success' => false, 'message' => $body['message'] ?? 'Erreur Orange Money.'];

        } catch (\Throwable $e) {
            Log::error('OrangeMoney initiate error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Service Orange Money indisponible.'];
        }
    }

    // ── MTN Mobile Money ──────────────────────────────────────────────────

    private function initiateMtnMomo(array $params): array
    {
        try {
            // MTN MoMo API (Disbursements / Collections)
            $response = Http::withHeaders([
                'X-Reference-Id'      => $params['reference'],
                'X-Target-Environment'=> config('services.mtn_momo.environment', 'sandbox'),
                'Ocp-Apim-Subscription-Key' => config('services.mtn_momo.subscription_key'),
                'Content-Type'        => 'application/json',
            ])->withToken(config('services.mtn_momo.api_key'))
            ->post(config('services.mtn_momo.base_url') . '/collection/v1_0/requesttopay', [
                'amount'       => (string) $params['amount'],
                'currency'     => 'XOF',
                'externalId'   => $params['reference'],
                'payer'        => ['partyIdType' => 'MSISDN', 'partyId' => ltrim($params['phone'], '+')],
                'payerMessage' => $params['description'],
                'payeeNote'    => 'AutoPlatform',
            ]);

            if ($response->status() === 202) {
                return [
                    'success'   => true,
                    'message'   => 'Demande de paiement envoyée. Confirmez sur votre téléphone.',
                    'reference' => $params['reference'],
                ];
            }

            return ['success' => false, 'message' => 'Erreur MTN MoMo.'];

        } catch (\Throwable $e) {
            Log::error('MTN MoMo initiate error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Service MTN MoMo indisponible.'];
        }
    }

    // ── Wave ──────────────────────────────────────────────────────────────

    private function initiateWave(array $params): array
    {
        try {
            $response = Http::withToken(config('services.wave.api_key'))
                ->post('https://api.wave.com/v1/checkout/sessions', [
                    'currency'    => 'XOF',
                    'amount'      => $params['amount'],
                    'error_url'   => config('app.url') . '/paiement/erreur',
                    'success_url' => config('app.url') . '/paiement/succes',
                    'client_reference' => $params['reference'],
                ]);

            $body = $response->json();

            if (isset($body['wave_launch_url'])) {
                return [
                    'success'     => true,
                    'payment_url' => $body['wave_launch_url'],
                    'session_id'  => $body['id'] ?? null,
                ];
            }

            return ['success' => false, 'message' => $body['message'] ?? 'Erreur Wave.'];

        } catch (\Throwable $e) {
            Log::error('Wave initiate error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Service Wave indisponible.'];
        }
    }

    // ── Moov Money ────────────────────────────────────────────────────────

    private function initiateMoovMoney(array $params): array
    {
        // À implémenter selon l'API Moov Africa CI
        return [
            'success'   => false,
            'message'   => 'Moov Money en cours d\'intégration.',
        ];
    }

    // ── Traitement webhook (succès paiement) ──────────────────────────────

    /**
     * Appelé par le webhook quand un paiement est confirmé
     */
    public function handleSuccess(string $reference, string $operatorRef, array $rawResponse): bool
    {
        $transaction = PaymentTransaction::where('reference', $reference)
            ->where('status', 'pending')
            ->first();

        if (!$transaction) {
            Log::warning("Transaction introuvable ou déjà traitée: {$reference}");
            return false;
        }

        $transaction->update([
            'status'                  => 'success',
            'operator_reference'      => $operatorRef,
            'operator_response'       => $rawResponse,
            'paid_at'                 => now(),
        ]);

        // Activer l'abonnement
        $this->activateSubscription($transaction);

        return true;
    }

    /**
     * Appelé par le webhook quand un paiement échoue
     */
    public function handleFailure(string $reference, string $reason, array $rawResponse): void
    {
        PaymentTransaction::where('reference', $reference)->update([
            'status'            => 'failed',
            'failure_reason'    => $reason,
            'operator_response' => $rawResponse,
        ]);
    }

    /**
     * Activer l'abonnement après paiement réussi
     */
    private function activateSubscription(PaymentTransaction $transaction): void
    {
        $payer = $transaction->payer;
        if (!$payer) return;

        // Déterminer le plan selon le type de payer
        $isPro  = in_array(get_class($payer), [\App\Models\StationOwner::class, \App\Models\GarageOwner::class]);
        $plan   = $isPro ? 'station_pro' : 'user_premium';
        $months = 1;
        $expires= now()->addMonth();

        // Créer l'abonnement
        Subscription::create([
            'subscribable_type'   => get_class($payer),
            'subscribable_id'     => $payer->id,
            'plan'                => $plan,
            'amount'              => $transaction->amount,
            'starts_at'           => now(),
            'expires_at'          => $expires,
            'status'              => 'active',
            'payment_method'      => $transaction->payment_method,
            'payment_reference'   => $transaction->reference,
            'paid_at'             => now(),
        ]);

        // Mettre à jour le type d'abonnement sur le modèle
        if ($payer instanceof User) {
            $payer->update([
                'subscription_type'       => 'premium',
                'subscription_expires_at' => $expires,
            ]);
        } elseif (method_exists($payer, 'stations')) {
            $payer->stations()->update([
                'subscription_type'       => 'pro',
                'subscription_expires_at' => $expires,
            ]);
        }

        // Envoyer notification de confirmation
        if ($payer instanceof User && $payer->fcm_token) {
            app(FirebaseService::class)->sendToDevice($payer->fcm_token, [
                'title' => 'Abonnement Premium activé !',
                'body'  => "Votre abonnement Premium est actif jusqu'au " . $expires->format('d/m/Y') . '.',
            ]);
        }
    }
}
