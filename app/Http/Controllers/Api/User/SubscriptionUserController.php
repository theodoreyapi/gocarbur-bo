<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Models\Subscription;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionUserController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * GET /subscription/plans
     */
    public function plans(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                [
                    'key'         => 'user_free',
                    'label'       => 'Gratuit',
                    'price'       => 0,
                    'currency'    => 'FCFA',
                    'features'    => [
                        'Accès aux stations',
                        'Accès aux garages',
                        '1 véhicule',
                        'Rappels simples',
                    ],
                    'limitations' => ['Publicités', '1 véhicule max'],
                ],
                [
                    'key'          => 'user_premium',
                    'label'        => 'Premium',
                    'price'        => 1500,
                    'currency'     => 'FCFA',
                    'billing_cycle'=> 'mensuel',
                    'features'     => [
                        'Sans publicité',
                        'Véhicules illimités',
                        'Alertes carburant moins cher',
                        'Statistiques carburant',
                        'Rappels avancés',
                        'Carnet d\'entretien complet',
                        'Assistance automobile rapide',
                        'Réductions partenaires',
                    ],
                ],
            ],
        ]);
    }

    /**
     * POST /subscription/initiate
     * Initier un paiement Mobile Money
     */
    public function initiate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'plan'           => 'required|in:user_premium',
            'payment_method' => 'required|in:orange_money,mtn_money,moov_money,wave,cinetpay',
            'phone'          => 'required|string|max:20',
        ]);

        $user      = $request->user();
        $amount    = 1500; // FCFA
        $reference = 'SUB-' . strtoupper(Str::random(12));

        // Créer la transaction en attente
        $transaction = PaymentTransaction::create([
            'reference'      => $reference,
            'payer_type'     => User::class,
            'payer_id'       => $user->id,
            'amount'         => $amount,
            'payment_method' => $data['payment_method'],
            'status'         => 'pending',
            'phone_payer'    => $data['phone'],
        ]);

        // Initier le paiement chez l'opérateur
        $result = $this->paymentService->initiate([
            'reference'  => $reference,
            'amount'     => $amount,
            'phone'      => $data['phone'],
            'method'     => $data['payment_method'],
            'description'=> 'Abonnement Premium AutoPlatform',
        ]);

        if (!$result['success']) {
            $transaction->update(['status' => 'failed', 'failure_reason' => $result['message']]);
            return response()->json(['success' => false, 'message' => $result['message']], 422);
        }

        return response()->json([
            'success'       => true,
            'message'       => 'Paiement initié. Validez sur votre téléphone.',
            'reference'     => $reference,
            'payment_url'   => $result['payment_url'] ?? null,
            'ussd_code'     => $result['ussd_code'] ?? null,
        ]);
    }

    /**
     * GET /subscription/status/{reference}
     */
    public function status(Request $request, string $reference): JsonResponse
    {
        $transaction = PaymentTransaction::where('reference', $reference)
            ->where('payer_type', \App\Models\User::class)
            ->where('payer_id', $request->user()->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => [
                'reference' => $transaction->reference,
                'status'    => $transaction->status,
                'amount'    => $transaction->amount,
                'paid_at'   => $transaction->paid_at,
            ],
        ]);
    }

    /**
     * POST /subscription/cancel
     */
    public function cancel(Request $request): JsonResponse
    {
        $subscription = $request->user()->subscriptions()
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json(['success' => false, 'message' => 'Aucun abonnement actif.'], 404);
        }

        $subscription->update([
            'status'               => 'cancelled',
            'cancellation_reason'  => $request->input('reason', 'Annulé par l\'utilisateur'),
            'cancelled_at'         => now(),
        ]);

        // L'abonnement reste actif jusqu'à expires_at
        return response()->json([
            'success' => true,
            'message' => "Abonnement annulé. Actif jusqu'au {$subscription->expires_at->format('d/m/Y')}.",
        ]);
    }

    /**
     * GET /subscription/history
     */
    public function history(Request $request): JsonResponse
    {
        $history = $request->user()->subscriptions()
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $history]);
    }
}
