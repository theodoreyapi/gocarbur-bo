<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\UserCarbur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationsController extends Controller
{
    /**
     * Dashboard notifications.
     */
    public function index()
    {
        /* ── KPIs ─────────────────────────────────── */
        $stats = [
            'sent_today'    => Notification::pushSent()->whereDate('push_sent_at', today())->count(),
            'sent_month'    => Notification::pushSent()->whereMonth('push_sent_at', now()->month)->count(),
            'unread_total'  => Notification::unread()->count(),
            'delivery_rate' => $this->computeDeliveryRate(),
        ];

        /* ── Historique broadcasts (groupés par title+date) ─ */
        // On considère les notifs de type broadcast envoyées en push
        $broadcasts = Notification::pushSent()
            ->whereIn('type', ['broadcast', 'promotion', 'system', 'fuel_alert', 'conseil', 'reminder'])
            ->select(
                'title',
                'body',
                'type',
                DB::raw('COUNT(*) as total_sent'),
                DB::raw('SUM(is_read) as total_read'),
                DB::raw('MAX(push_sent_at) as sent_at'),
                DB::raw('MIN(id_notification) as id_notification') // pour grouper
            )
            ->groupBy('title', 'body', 'type')
            ->orderByDesc('sent_at')
            ->limit(20)
            ->get();

        /* ── Stats par type (pour le graphe donut) ── */
        $typeStats = Notification::pushSent()
            ->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type');

        /* ── Villes disponibles (depuis users_carbur) ─ */
        $cities = UserCarbur::whereNotNull('city')
            ->distinct()
            ->pluck('city')
            ->sort()
            ->values();

        return view('pages.notifications', compact('stats', 'broadcasts', 'typeStats', 'cities'));
    }

    /**
     * Send a broadcast notification via FCM.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:100',
            'body'        => 'required|string|max:500',
            'type'        => 'required|in:document_expiry,fuel_alert,promotion,reminder,system,conseil,broadcast',
            'target'      => 'required|in:all,premium,city,specific',
            'target_city' => 'nullable|required_if:target,city|string|max:100',
            'target_user' => 'nullable|required_if:target,specific|string',
            'action_url'  => 'nullable|string|max:300',
        ]);

        // Résoudre les destinataires
        $usersQuery = UserCarbur::where('is_active', true)->whereNotNull('fcm_token');

        $usersQuery = match ($validated['target']) {
            'premium'  => $usersQuery->where('subscription_type', 'premium'),
            'city'     => $usersQuery->where('city', $validated['target_city']),
            'specific' => $usersQuery->where('phone', $validated['target_user'])
                ->orWhere('email', $validated['target_user']),
            default    => $usersQuery,
        };

        $users = $usersQuery->get(['id_user_carbu', 'fcm_token']);
        $now   = now();
        $count = 0;

        // Insérer une notif par utilisateur (chunked)
        $users->chunk(500)->each(function ($chunk) use ($validated, $now, &$count) {
            $rows = $chunk->map(fn($u) => [
                'user_id'      => $u->id_user_carbu,
                'type'         => $validated['type'],
                'title'        => $validated['title'],
                'body'         => $validated['body'],
                'action_url'   => $validated['action_url'] ?? null,
                'is_push_sent' => true,
                'push_sent_at' => $now,
                'created_at'   => $now,
                'updated_at'   => $now,
            ])->toArray();

            Notification::insert($rows);
            $count += count($rows);

            // TODO : envoyer via Firebase FCM
            // $this->sendFcmBatch($chunk->pluck('fcm_token'), $validated['title'], $validated['body']);
        });

        return redirect()->route('notifications.index')
            ->with('toast_success', "Notification envoyée à {$count} utilisateur(s).");
    }

    /**
     * Delete a broadcast (soft via mass-delete on matching title+body).
     */
    public function destroy(string $id)
    {
        // id_notification est le MIN de chaque groupe — on supprime le groupe par title+body
        $ref = Notification::find($id);
        if ($ref) {
            Notification::where('title', $ref->title)->where('body', $ref->body)->delete();
        }

        return redirect()->route('notifications.index')
            ->with('toast_error', 'Broadcast supprimé.');
    }

    /* ── Private helpers ─────────────────────────── */

    private function computeDeliveryRate(): float
    {
        $sent = Notification::pushSent()->count();
        if ($sent === 0) return 0;
        $read = Notification::pushSent()->where('is_read', true)->count();
        return round(($read / $sent) * 100, 1);
    }
}
