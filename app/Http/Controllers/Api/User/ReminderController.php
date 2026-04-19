<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReminderController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX — Rappels actifs
    // GET /connecte/reminders
    // ─────────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id_user_carbu;
        $limit  = $request->input('limit', 20);
        $page   = max(1, (int) $request->input('page', 1));

        $query = DB::table('reminders')
            ->where('user_id', $userId)
            ->where('is_dismissed', false)
            ->orderBy('remind_at');

        $total = $query->count();
        $items = $query->offset(($page - 1) * $limit)->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => ['total' => $total, 'page' => $page, 'limit' => $limit],
        ]);
    }

    // ─────────────────────────────────────────────
    // UPCOMING — Rappels à venir (dashboard)
    // GET /connecte/reminders/upcoming
    // ─────────────────────────────────────────────
    public function upcoming(Request $request): JsonResponse
    {
        $userId = $request->user()->id_user_carbu;
        $days   = $request->input('days', 30); // horizon en jours

        $reminders = DB::table('reminders')
            ->where('user_id', $userId)
            ->where('is_dismissed', false)
            ->where('remind_at', '>=', now()->toDateString())
            ->where('remind_at', '<=', now()->addDays($days)->toDateString())
            ->orderBy('remind_at')
            ->get();

        return response()->json(['success' => true, 'data' => $reminders]);
    }

    // ─────────────────────────────────────────────
    // STORE — Créer un rappel
    // POST /connecte/reminders
    // ─────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $userId = $request->user()->id_user_carbu;

        $validated = $request->validate([
            'type'               => 'required|in:expiration_assurance,expiration_visite_technique,expiration_permis,vidange,entretien,controle_pneus,controle_batterie,revision,personnalise',
            'title'              => 'required|string|max:150',
            'notes'              => 'nullable|string|max:500',
            'remind_at'          => 'required|date|after_or_equal:today',
            'remind_before_days' => 'sometimes|integer|min:1|max:365',
            'vehicle_id'         => 'nullable|integer|exists:vehicles,id_vehicule',
            'document_id'        => 'nullable|integer|exists:documents,id_doc',
            'is_recurring'       => 'sometimes|boolean',
            'recurrence'         => 'required_if:is_recurring,true|nullable|in:mensuel,trimestriel,annuel',
        ]);

        // Vérifier que le véhicule appartient à l'user si fourni
        if (!empty($validated['vehicle_id'])) {
            $ok = DB::table('vehicles')
                ->where('id_vehicule', $validated['vehicle_id'])
                ->where('user_id', $userId)
                ->whereNull('deleted_at')
                ->exists();
            if (!$ok) {
                return response()->json(['success' => false, 'message' => 'Véhicule invalide.'], 422);
            }
        }

        $id = DB::table('reminders')->insertGetId(array_merge($validated, [
            'user_id'    => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        $reminder = DB::table('reminders')->where('id_reminder', $id)->first();

        return response()->json(['success' => true, 'message' => 'Rappel créé.', 'data' => $reminder], 201);
    }

    // ─────────────────────────────────────────────
    // SHOW
    // GET /connecte/reminders/{id}
    // ─────────────────────────────────────────────
    public function show(Request $request, int $id): JsonResponse
    {
        $reminder = $this->findUserReminder($request->user()->id_user_carbu, $id);

        if (!$reminder) {
            return response()->json(['success' => false, 'message' => 'Rappel introuvable.'], 404);
        }

        return response()->json(['success' => true, 'data' => $reminder]);
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // PUT /connecte/reminders/{id}
    // ─────────────────────────────────────────────
    public function update(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()->id_user_carbu;

        if (!$this->findUserReminder($userId, $id)) {
            return response()->json(['success' => false, 'message' => 'Rappel introuvable.'], 404);
        }

        $validated = $request->validate([
            'title'              => 'sometimes|string|max:150',
            'notes'              => 'sometimes|nullable|string|max:500',
            'remind_at'          => 'sometimes|date',
            'remind_before_days' => 'sometimes|integer|min:1|max:365',
            'is_recurring'       => 'sometimes|boolean',
            'recurrence'         => 'sometimes|nullable|in:mensuel,trimestriel,annuel',
        ]);

        DB::table('reminders')
            ->where('id_reminder', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        $updated = DB::table('reminders')->where('id_reminder', $id)->first();

        return response()->json(['success' => true, 'message' => 'Rappel mis à jour.', 'data' => $updated]);
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // DELETE /connecte/reminders/{id}
    // ─────────────────────────────────────────────
    public function destroy(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()->id_user_carbu;

        if (!$this->findUserReminder($userId, $id)) {
            return response()->json(['success' => false, 'message' => 'Rappel introuvable.'], 404);
        }

        DB::table('reminders')->where('id_reminder', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Rappel supprimé.']);
    }

    // ─────────────────────────────────────────────
    // DISMISS — Ignorer un rappel
    // PATCH /connecte/reminders/{id}/dismiss
    // ─────────────────────────────────────────────
    public function dismiss(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()->id_user_carbu;

        if (!$this->findUserReminder($userId, $id)) {
            return response()->json(['success' => false, 'message' => 'Rappel introuvable.'], 404);
        }

        DB::table('reminders')
            ->where('id_reminder', $id)
            ->update(['is_dismissed' => true, 'updated_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Rappel ignoré.']);
    }

    private function findUserReminder(int $userId, int $id): ?object
    {
        return DB::table('reminders')
            ->where('id_reminder', $id)
            ->where('user_id', $userId)
            ->first();
    }
}
