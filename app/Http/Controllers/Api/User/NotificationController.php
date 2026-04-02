<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * GET /notifications
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->unread_only, fn($q) => $q->where('is_read', false))
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return response()->json(['success' => true, 'data' => $notifications]);
    }

    /**
     * GET /notifications/unread-count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = $request->user()->notifications()
            ->where('is_read', false)
            ->count();

        return response()->json(['success' => true, 'unread_count' => $count]);
    }

    /**
     * PATCH /notifications/{id}/read
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $notif = $request->user()->notifications()->findOrFail($id);

        if (!$notif->is_read) {
            $notif->update(['is_read' => true, 'read_at' => now()]);
        }

        return response()->json(['success' => true, 'message' => 'Notification marquée comme lue.']);
    }

    /**
     * PATCH /notifications/read-all
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $count = $request->user()->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true, 'message' => "{$count} notifications marquées comme lues."]);
    }

    /**
     * DELETE /notifications/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $request->user()->notifications()->findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Notification supprimée.']);
    }

    /**
     * DELETE /notifications
     */
    public function destroyAll(Request $request): JsonResponse
    {
        $count = $request->user()->notifications()->delete();
        return response()->json(['success' => true, 'message' => "{$count} notifications supprimées."]);
    }
}
