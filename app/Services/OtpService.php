<?php

namespace App\Services;

use App\Models\OtpCode;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Génère un code OTP à 6 chiffres, le persiste et l'envoie par SMS
     */
    public function generate(string $phone): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'phone'      => $phone,
            'code'       => $code,
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->send($phone, $code);

        return $code;
    }

    /**
     * Envoie le SMS via Infobip (ou autre gateway configuré)
     */
    private function send(string $phone, string $code): void
    {
        $gateway = config('services.sms.gateway', 'infobip');

        try {
            match ($gateway) {
                'infobip' => $this->sendViaInfobip($phone, $code),
                'twilio'  => $this->sendViaTwilio($phone, $code),
                'orange'  => $this->sendViaOrangeSms($phone, $code),
                default   => Log::info("OTP [{$code}] pour {$phone} (mode dev)"),
            };
        } catch (\Throwable $e) {
            Log::error("Échec envoi OTP: {$e->getMessage()}", ['phone' => $phone]);
        }
    }

    private function sendViaInfobip(string $phone, string $code): void
    {
        Http::withHeaders([
            'Authorization' => 'App ' . config('services.infobip.api_key'),
            'Content-Type'  => 'application/json',
        ])->post(config('services.infobip.base_url') . '/sms/2/text/advanced', [
            'messages' => [[
                'from'         => 'AutoPlatform',
                'destinations' => [['to' => $phone]],
                'text'         => "Votre code de connexion AutoPlatform : {$code}. Valide 5 minutes.",
            ]],
        ])->throw();
    }

    private function sendViaTwilio(string $phone, string $code): void
    {
        Http::withBasicAuth(
            config('services.twilio.sid'),
            config('services.twilio.token')
        )->post("https://api.twilio.com/2010-04-01/Accounts/" . config('services.twilio.sid') . "/Messages.json", [
            'From' => config('services.twilio.from'),
            'To'   => $phone,
            'Body' => "AutoPlatform - Code: {$code}. Valide 5 minutes.",
        ])->throw();
    }

    private function sendViaOrangeSms(string $phone, string $code): void
    {
        // Implémentation Orange SMS CI
        Http::withToken(config('services.orange_sms.token'))
            ->post(config('services.orange_sms.url'), [
                'outboundSMSMessageRequest' => [
                    'address'              => "tel:{$phone}",
                    'senderAddress'        => 'tel:' . config('services.orange_sms.sender'),
                    'outboundSMSTextMessage'=> ['message' => "AutoPlatform code: {$code}"],
                ],
            ])->throw();
    }
}
