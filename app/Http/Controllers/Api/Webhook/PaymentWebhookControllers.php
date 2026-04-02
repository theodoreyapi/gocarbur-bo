<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

// ═══════════════════════════════════════════════════════════════
// CinetPay Webhook
// ═══════════════════════════════════════════════════════════════

class CinetPayController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * POST /webhooks/cinetpay
     */
    public function handle(Request $request): Response
    {
        Log::info('CinetPay webhook reçu', $request->all());

        $data = $request->all();

        // Vérifier la signature CinetPay
        $expectedHash = hash_hmac(
            'sha256',
            $data['cpm_site_id'] . $data['cpm_trans_id'] . $data['cpm_amount'] . $data['cpm_currency'],
            config('services.cinetpay.secret_key')
        );

        if (!hash_equals($expectedHash, $data['cpm_signature'] ?? '')) {
            Log::warning('CinetPay: signature invalide', ['data' => $data]);
            return response('Signature invalide', 403);
        }

        $reference   = $data['cpm_trans_id']   ?? null;
        $status      = $data['cpm_result']      ?? null;
        $operatorRef = $data['cpm_payid']       ?? null;

        if (!$reference) {
            return response('Référence manquante', 400);
        }

        if ($status === '00') {
            // Paiement réussi
            $this->paymentService->handleSuccess($reference, $operatorRef, $data);
        } else {
            // Paiement échoué
            $reason = $data['cpm_error_message'] ?? "Code erreur: {$status}";
            $this->paymentService->handleFailure($reference, $reason, $data);
        }

        return response('OK', 200);
    }
}

// ═══════════════════════════════════════════════════════════════
// Orange Money Webhook
// ═══════════════════════════════════════════════════════════════

class OrangeMoneyController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * POST /webhooks/orange-money
     */
    public function handle(Request $request): Response
    {
        Log::info('OrangeMoney webhook reçu', $request->all());

        $data = $request->all();

        // Vérifier le token de notification Orange
        $receivedToken = $request->header('X-Orange-Notify-Token');
        $expectedToken = config('services.orange_money.notify_token');

        if ($receivedToken !== $expectedToken) {
            Log::warning('OrangeMoney: token invalide');
            return response('Token invalide', 403);
        }

        $reference   = $data['order_id']      ?? $data['reference']     ?? null;
        $status      = $data['status']         ?? null;
        $operatorRef = $data['txnid']          ?? $data['pay_token']    ?? null;

        if (!$reference) {
            return response('Référence manquante', 400);
        }

        if (in_array($status, ['SUCCESS', 'ACCEPTED', '60000'])) {
            $this->paymentService->handleSuccess($reference, $operatorRef, $data);
        } else {
            $this->paymentService->handleFailure($reference, "Statut: {$status}", $data);
        }

        return response('OK', 200);
    }
}

// ═══════════════════════════════════════════════════════════════
// MTN MoMo Webhook
// ═══════════════════════════════════════════════════════════════

class MtnMomoController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * POST /webhooks/mtn-momo
     */
    public function handle(Request $request): Response
    {
        Log::info('MTN MoMo webhook reçu', $request->all());

        $data = $request->all();

        // Vérifier la signature HMAC MTN
        $signature = $request->header('X-Callback-Signature');
        $body      = $request->getContent();
        $expected  = base64_encode(hash_hmac('sha256', $body, config('services.mtn_momo.callback_secret'), true));

        if (!hash_equals($expected, $signature ?? '')) {
            Log::warning('MTN MoMo: signature invalide');
            return response('Signature invalide', 403);
        }

        $reference   = $data['externalId']         ?? null;
        $status      = $data['status']              ?? null;
        $operatorRef = $data['financialTransactionId'] ?? null;

        if (!$reference) {
            return response('Référence manquante', 400);
        }

        if ($status === 'SUCCESSFUL') {
            $this->paymentService->handleSuccess($reference, $operatorRef, $data);
        } else {
            $reason = $data['reason'] ?? "Statut: {$status}";
            $this->paymentService->handleFailure($reference, $reason, $data);
        }

        return response('OK', 200);
    }
}

// ═══════════════════════════════════════════════════════════════
// Wave Webhook
// ═══════════════════════════════════════════════════════════════

class WaveController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * POST /webhooks/wave
     */
    public function handle(Request $request): Response
    {
        Log::info('Wave webhook reçu', $request->all());

        $data = $request->all();

        // Vérifier signature Wave (HMAC SHA-256)
        $signature = $request->header('Wave-Signature');
        $body      = $request->getContent();
        $expected  = 'sha256=' . hash_hmac('sha256', $body, config('services.wave.webhook_secret'));

        if (!hash_equals($expected, $signature ?? '')) {
            Log::warning('Wave: signature invalide');
            return response('Signature invalide', 403);
        }

        $eventType   = $data['type']                    ?? null;
        $reference   = $data['data']['client_reference'] ?? null;
        $operatorRef = $data['data']['id']               ?? null;
        $status      = $data['data']['payment_status']   ?? null;

        if (!$reference) {
            return response('Référence manquante', 400);
        }

        match ($eventType) {
            'checkout.session.completed' => $this->paymentService->handleSuccess($reference, $operatorRef, $data),
            'checkout.session.expired',
            'checkout.session.failed'    => $this->paymentService->handleFailure($reference, "Wave: {$status}", $data),
            default                      => Log::info("Wave: event ignoré: {$eventType}"),
        };

        return response('OK', 200);
    }
}
