<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    /**
     * GET /reminders
     */
    public function index(Request $request): JsonResponse
    {
        $reminders = $request->user()->reminders()
            ->with(['vehicle:id,brand,model,plate_number', 'document:id,type,expiry_date'])
            ->where('is_dismissed', false)
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->orderBy('remind_at')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $reminders]);
    }

    /**
     * GET /reminders/upcoming
     * Pour le dashboard : rappels dans les 60 prochains jours
     */
    public function upcoming(Request $request): JsonResponse
    {
        $reminders = $request->user()->reminders()
            ->with(['vehicle:id,brand,model', 'document:id,type'])
            ->where('is_dismissed', false)
            ->where('is_sent', false)
            ->whereBetween('remind_at', [now(), now()->addDays(60)])
            ->orderBy('remind_at')
            ->limit(10)
            ->get();

        return response()->json(['success' => true, 'data' => $reminders]);
    }

    /**
     * POST /reminders
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'vehicle_id'         => 'nullable|integer|exists:vehicles,id',
            'document_id'        => 'nullable|integer|exists:documents,id',
            'type'               => 'required|in:expiration_assurance,expiration_visite_technique,expiration_permis,vidange,entretien,controle_pneus,controle_batterie,revision,personnalise',
            'title'              => 'required|string|max:150',
            'notes'              => 'nullable|string|max:500',
            'remind_at'          => 'required|date|after:today',
            'remind_before_days' => 'nullable|integer|min:1|max:365',
            'is_recurring'       => 'boolean',
            'recurrence'         => 'nullable|in:mensuel,trimestriel,annuel',
        ]);

        // Vérifier que le véhicule appartient à l'utilisateur
        if (!empty($data['vehicle_id'])) {
            $request->user()->vehicles()->findOrFail($data['vehicle_id']);
        }

        $data['user_id'] = $request->user()->id;
        $reminder = Reminder::create($data);

        return response()->json(['success' => true, 'message' => 'Rappel créé.', 'data' => $reminder], 201);
    }

    /**
     * GET /reminders/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $reminder = $request->user()->reminders()
            ->with(['vehicle:id,brand,model', 'document:id,type,expiry_date'])
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $reminder]);
    }

    /**
     * PUT /reminders/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $reminder = $request->user()->reminders()->findOrFail($id);

        $data = $request->validate([
            'title'              => 'sometimes|string|max:150',
            'notes'              => 'nullable|string|max:500',
            'remind_at'          => 'sometimes|date',
            'remind_before_days' => 'nullable|integer|min:1|max:365',
            'is_recurring'       => 'boolean',
            'recurrence'         => 'nullable|in:mensuel,trimestriel,annuel',
        ]);

        $reminder->update($data);

        return response()->json(['success' => true, 'message' => 'Rappel mis à jour.', 'data' => $reminder->fresh()]);
    }

    /**
     * DELETE /reminders/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $reminder = $request->user()->reminders()->findOrFail($id);
        $reminder->delete();

        return response()->json(['success' => true, 'message' => 'Rappel supprimé.']);
    }

    /**
     * PATCH /reminders/{id}/dismiss
     */
    public function dismiss(Request $request, int $id): JsonResponse
    {
        $reminder = $request->user()->reminders()->findOrFail($id);
        $reminder->update(['is_dismissed' => true]);

        return response()->json(['success' => true, 'message' => 'Rappel ignoré.']);
    }
}
