<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    private string $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    private string $fcmV1Url;
    private string $serverKey;
    private string $projectId;

    public function __construct()
    {
        $this->serverKey  = config('services.firebase.server_key');
        $this->projectId  = config('services.firebase.project_id');
        $this->fcmV1Url   = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
    }

    /**
     * Envoyer une notification à un seul appareil
     */
    public function sendToDevice(string $token, array $notification, array $data = []): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "key={$this->serverKey}",
                'Content-Type'  => 'application/json',
            ])->post($this->fcmUrl, [
                'to'           => $token,
                'notification' => [
                    'title' => $notification['title'],
                    'body'  => $notification['body'],
                    'sound' => 'default',
                    'badge' => 1,
                    'icon'  => 'ic_notification',
                    'color' => '#FF6B35',
                ],
                'data'         => array_merge($data, [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'action_url'   => $notification['action_url'] ?? null,
                ]),
                'android'      => [
                    'priority'     => 'high',
                    'notification' => ['channel_id' => 'autoplatform_default'],
                ],
                'apns'         => [
                    'payload' => ['aps' => ['alert' => ['title' => $notification['title'], 'body' => $notification['body']], 'badge' => 1, 'sound' => 'default']],
                ],
            ]);

            $body = $response->json();

            return [
                'success'       => $response->successful() && ($body['success'] ?? 0) > 0,
                'success_count' => $body['success'] ?? 0,
                'failure_count' => $body['failure'] ?? 0,
                'response'      => $body,
            ];
        } catch (\Throwable $e) {
            Log::error('Firebase sendToDevice error: ' . $e->getMessage(), ['token' => $token]);
            return ['success' => false, 'success_count' => 0, 'failure_count' => 1];
        }
    }

    /**
     * Envoyer à plusieurs appareils (max 500 tokens par appel)
     */
    public function sendMulticast(array $tokens, array $notification, array $data = []): array
    {
        if (empty($tokens)) {
            return ['success_count' => 0, 'failure_count' => 0];
        }

        // Firebase limite à 500 tokens par requête
        $chunks        = array_chunk($tokens, 500);
        $totalSuccess  = 0;
        $totalFailure  = 0;

        foreach ($chunks as $chunk) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "key={$this->serverKey}",
                    'Content-Type'  => 'application/json',
                ])->post($this->fcmUrl, [
                    'registration_ids' => $chunk,
                    'notification'     => [
                        'title' => $notification['title'],
                        'body'  => $notification['body'],
                        'sound' => 'default',
                        'icon'  => 'ic_notification',
                        'color' => '#FF6B35',
                    ],
                    'data'             => array_merge($data, [
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        'action_url'   => $notification['action_url'] ?? null,
                    ]),
                    'android'          => ['priority' => 'high'],
                ]);

                $body          = $response->json();
                $totalSuccess += $body['success'] ?? 0;
                $totalFailure += $body['failure'] ?? 0;

            } catch (\Throwable $e) {
                Log::error('Firebase multicast error: ' . $e->getMessage());
                $totalFailure += count($chunk);
            }
        }

        return [
            'success_count' => $totalSuccess,
            'failure_count' => $totalFailure,
        ];
    }

    /**
     * Envoyer une notification à un topic (ex: "ville_abidjan")
     */
    public function sendToTopic(string $topic, array $notification, array $data = []): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "key={$this->serverKey}",
                'Content-Type'  => 'application/json',
            ])->post($this->fcmUrl, [
                'to'           => "/topics/{$topic}",
                'notification' => [
                    'title' => $notification['title'],
                    'body'  => $notification['body'],
                    'sound' => 'default',
                ],
                'data'         => $data,
            ]);

            return ['success' => $response->successful(), 'response' => $response->json()];
        } catch (\Throwable $e) {
            Log::error('Firebase sendToTopic error: ' . $e->getMessage());
            return ['success' => false];
        }
    }

    /**
     * Abonner un token à un topic
     */
    public function subscribeToTopic(string $token, string $topic): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "key={$this->serverKey}",
                'Content-Type'  => 'application/json',
            ])->post("https://iid.googleapis.com/iid/v1/{$token}/rel/topics/{$topic}");

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Firebase subscribeToTopic error: ' . $e->getMessage());
            return false;
        }
    }
}
