<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminNotificationController extends Controller
{
    public function __construct(private FirebaseService $firebase) {}

    /** POST /admin/notifications/broadcast */
    public function broadcast(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'      => 'required|string|max:100',
            'body'       => 'required|string|max:500',
            'type'       => 'required|in:system,broadcast,conseil,fuel_alert,promotion',
            'action_url' => 'nullable|string|max:255',
            'data'       => 'nullable|array',
        ]);

        $tokens = User::where('is_active', true)
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token', 'id');

        // Envoyer en batch (Firebase limite à 500 tokens par requête)
        $chunks    = $tokens->chunk(500);
        $sent      = 0;
        $failed    = 0;

        foreach ($chunks as $chunk) {
            $result  = $this->firebase->sendMulticast($chunk->values()->toArray(), [
                'title'      => $data['title'],
                'body'       => $data['body'],
                'data'       => $data['data'] ?? [],
                'action_url' => $data['action_url'] ?? null,
            ]);
            $sent   += $result['success_count'];
            $failed += $result['failure_count'];
        }

        // Persister en base pour chaque utilisateur
        $notifData = array_map(fn($userId) => [
            'user_id'      => $userId,
            'type'         => $data['type'],
            'title'        => $data['title'],
            'body'         => $data['body'],
            'data'         => json_encode($data['data'] ?? []),
            'action_url'   => $data['action_url'] ?? null,
            'is_push_sent' => true,
            'push_sent_at' => now(),
            'created_at'   => now(),
            'updated_at'   => now(),
        ], $tokens->keys()->toArray());

        foreach (array_chunk($notifData, 1000) as $batch) {
            AppNotification::insert($batch);
        }

        return response()->json([
            'success'      => true,
            'message'      => "Notification envoyée à {$sent} utilisateurs.",
            'sent_count'   => $sent,
            'failed_count' => $failed,
        ]);
    }

    /** POST /admin/notifications/broadcast-city */
    public function broadcastCity(Request $request): JsonResponse
    {
        $data = $request->validate([
            'city'       => 'required|string|max:100',
            'title'      => 'required|string|max:100',
            'body'       => 'required|string|max:500',
            'type'       => 'required|string',
            'action_url' => 'nullable|string',
        ]);

        $tokens = User::where('is_active', true)
            ->where('city', 'like', "%{$data['city']}%")
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token');

        if ($tokens->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Aucun utilisateur dans cette ville.'], 404);
        }

        $result = $this->firebase->sendMulticast($tokens->toArray(), [
            'title' => $data['title'],
            'body'  => $data['body'],
        ]);

        return response()->json([
            'success'    => true,
            'city'       => $data['city'],
            'sent_count' => $result['success_count'],
        ]);
    }

    /** POST /admin/notifications/send-to-user */
    public function sendToUser(Request $request): JsonResponse
    {
        $data = $request->validate([
            'user_id'    => 'required|exists:users,id',
            'title'      => 'required|string|max:100',
            'body'       => 'required|string|max:500',
            'type'       => 'required|string',
            'action_url' => 'nullable|string',
        ]);

        $user = User::findOrFail($data['user_id']);

        $notif = AppNotification::create([
            'user_id'      => $user->id,
            'type'         => $data['type'],
            'title'        => $data['title'],
            'body'         => $data['body'],
            'action_url'   => $data['action_url'] ?? null,
            'is_push_sent' => false,
        ]);

        if ($user->fcm_token) {
            $result = $this->firebase->sendToDevice($user->fcm_token, [
                'title' => $data['title'],
                'body'  => $data['body'],
            ]);
            $notif->update(['is_push_sent' => true, 'push_sent_at' => now()]);
        }

        return response()->json(['success' => true, 'message' => 'Notification envoyée.', 'data' => $notif]);
    }

    /** GET /admin/notifications/history */
    public function history(Request $request): JsonResponse
    {
        $history = AppNotification::where('type', 'broadcast')
            ->selectRaw('title, body, COUNT(*) as recipients, MIN(created_at) as sent_at')
            ->groupBy('title', 'body')
            ->orderByDesc('sent_at')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $history]);
    }
}
