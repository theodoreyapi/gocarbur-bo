<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    // GET /connecte/notifications
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id_user_carbu;
        $limit  = $request->input('limit', 20);
        $page   = max(1, (int) $request->input('page', 1));

        $query = DB::table('notifications')
            ->where('user_id', $userId)
            ->orderByDesc('created_at');

        $total = $query->count();
        $items = $query->offset(($page - 1) * $limit)->limit($limit)->get()
            ->map(fn ($n) => array_merge((array) $n, [
                'data' => $n->data ? json_decode($n->data, true) : null,
            ]));

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => ['total' => $total, 'page' => $page, 'limit' => $limit],
        ]);
    }

    // GET /connecte/notifications/unread-count
    public function unreadCount(Request $request): JsonResponse
    {
        $count = DB::table('notifications')
            ->where('user_id', $request->user()->id_user_carbu)
            ->where('is_read', false)
            ->count();

        return response()->json(['success' => true, 'data' => ['count' => $count]]);
    }

    // PATCH /connecte/notifications/{id}/read
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()->id_user_carbu;

        $exists = DB::table('notifications')
            ->where('id_notification', $id)->where('user_id', $userId)->exists();

        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Notification introuvable.'], 404);
        }

        DB::table('notifications')
            ->where('id_notification', $id)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Notification marquée comme lue.']);
    }

    // PATCH /connecte/notifications/read-all
    public function markAllAsRead(Request $request): JsonResponse
    {
        DB::table('notifications')
            ->where('user_id', $request->user()->id_user_carbu)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Toutes les notifications marquées comme lues.']);
    }

    // DELETE /connecte/notifications/{id}
    public function destroy(Request $request, int $id): JsonResponse
    {
        $deleted = DB::table('notifications')
            ->where('id_notification', $id)
            ->where('user_id', $request->user()->id_user_carbu)
            ->delete();

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Notification introuvable.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Notification supprimée.']);
    }

    // DELETE /connecte/notifications
    public function destroyAll(Request $request): JsonResponse
    {
        DB::table('notifications')
            ->where('user_id', $request->user()->id_user_carbu)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Toutes les notifications supprimées.']);
    }
}
