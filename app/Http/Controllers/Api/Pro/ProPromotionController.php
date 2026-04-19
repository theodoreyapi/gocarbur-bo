<?php

namespace App\Http\Controllers\Api\Pro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProPromotionController extends Controller
{
    // ══════════════════════════════════════════════
    // STATION PROMOTIONS
    // ══════════════════════════════════════════════

    // GET /pro/subscription/stations/{stationId}/promotions
    public function indexStation(Request $request, int $stationId): JsonResponse
    {
        if (!$this->ownsStation($request->user()->id_station_owner, $stationId)) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $promos = DB::table('promotions')
            ->where('promotable_type', 'App\Models\Station')
            ->where('promotable_id', $stationId)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['success' => true, 'data' => $promos]);
    }

    // POST /pro/subscription/stations/{stationId}/promotions
    public function storeStation(Request $request, int $stationId): JsonResponse
    {
        $this->requireProPlanStation($request);

        if (!$this->ownsStation($request->user()->id_station_owner, $stationId)) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $validated = $this->validatePromotion($request);

        $id = DB::table('promotions')->insertGetId(array_merge($validated, [
            'promotable_type' => 'App\Models\Station',
            'promotable_id'   => $stationId,
            'is_active'       => true,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]));

        $promo = DB::table('promotions')->where('id_promotion', $id)->first();

        return response()->json(['success' => true, 'message' => 'Promotion créée.', 'data' => $promo], 201);
    }

    // PUT /pro/subscription/stations/{stationId}/promotions/{id}
    public function updateStation(Request $request, int $stationId, int $id): JsonResponse
    {
        $this->requireProPlanStation($request);

        if (!$this->ownsStation($request->user()->id_station_owner, $stationId)) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $promo = DB::table('promotions')
            ->where('id_promotion', $id)
            ->where('promotable_type', 'App\Models\Station')
            ->where('promotable_id', $stationId)
            ->whereNull('deleted_at')
            ->first();

        if (!$promo) {
            return response()->json(['success' => false, 'message' => 'Promotion introuvable.'], 404);
        }

        $validated = $this->validatePromotion($request, partial: true);

        DB::table('promotions')
            ->where('id_promotion', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        return response()->json(['success' => true, 'message' => 'Promotion mise à jour.', 'data' => DB::table('promotions')->where('id_promotion', $id)->first()]);
    }

    // DELETE /pro/subscription/stations/{stationId}/promotions/{id}
    public function destroyStation(Request $request, int $stationId, int $id): JsonResponse
    {
        $this->requireProPlanStation($request);

        if (!$this->ownsStation($request->user()->id_station_owner, $stationId)) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $deleted = DB::table('promotions')
            ->where('id_promotion', $id)
            ->where('promotable_type', 'App\Models\Station')
            ->where('promotable_id', $stationId)
            ->whereNull('deleted_at')
            ->update(['deleted_at' => now()]);

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Promotion introuvable.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Promotion supprimée.']);
    }

    // ══════════════════════════════════════════════
    // GARAGE PROMOTIONS
    // ══════════════════════════════════════════════

    // GET /pro/subscription/garages/{garageId}/promotions
    public function indexGarage(Request $request, int $garageId): JsonResponse
    {
        if (!$this->ownsGarage($request->user()->id_gara_owner, $garageId)) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
        }

        $promos = DB::table('promotions')
            ->where('promotable_type', 'App\Models\Garage')
            ->where('promotable_id', $garageId)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['success' => true, 'data' => $promos]);
    }

    // POST /pro/subscription/garages/{garageId}/promotions
    public function storeGarage(Request $request, int $garageId): JsonResponse
    {
        $this->requireProPlanGarage($request);

        if (!$this->ownsGarage($request->user()->id_gara_owner, $garageId)) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
        }

        $validated = $this->validatePromotion($request);

        $id = DB::table('promotions')->insertGetId(array_merge($validated, [
            'promotable_type' => 'App\Models\Garage',
            'promotable_id'   => $garageId,
            'is_active'       => true,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]));

        $promo = DB::table('promotions')->where('id_promotion', $id)->first();

        return response()->json(['success' => true, 'message' => 'Promotion créée.', 'data' => $promo], 201);
    }

    // PUT /pro/subscription/garages/{garageId}/promotions/{id}
    public function updateGarage(Request $request, int $garageId, int $id): JsonResponse
    {
        $this->requireProPlanGarage($request);

        if (!$this->ownsGarage($request->user()->id_gara_owner, $garageId)) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
        }

        $promo = DB::table('promotions')
            ->where('id_promotion', $id)
            ->where('promotable_type', 'App\Models\Garage')
            ->where('promotable_id', $garageId)
            ->whereNull('deleted_at')
            ->first();

        if (!$promo) {
            return response()->json(['success' => false, 'message' => 'Promotion introuvable.'], 404);
        }

        $validated = $this->validatePromotion($request, partial: true);

        DB::table('promotions')
            ->where('id_promotion', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        return response()->json(['success' => true, 'message' => 'Promotion mise à jour.', 'data' => DB::table('promotions')->where('id_promotion', $id)->first()]);
    }

    // DELETE /pro/subscription/garages/{garageId}/promotions/{id}
    public function destroyGarage(Request $request, int $garageId, int $id): JsonResponse
    {
        $this->requireProPlanGarage($request);

        if (!$this->ownsGarage($request->user()->id_gara_owner, $garageId)) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
        }

        $deleted = DB::table('promotions')
            ->where('id_promotion', $id)
            ->where('promotable_type', 'App\Models\Garage')
            ->where('promotable_id', $garageId)
            ->whereNull('deleted_at')
            ->update(['deleted_at' => now()]);

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Promotion introuvable.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Promotion supprimée.']);
    }

    // ══════════════════════════════════════════════
    // TOGGLE — Activer / désactiver (station ou garage)
    // PATCH /pro/subscription/stations/{stationId}/promotions/{id}/toggle
    // PATCH /pro/subscription/garages/{garageId}/promotions/{id}/toggle
    // ══════════════════════════════════════════════
    public function toggle(Request $request, int $entityId, int $id): JsonResponse
    {
        // Déterminer si c'est une station ou un garage selon l'owner connecté
        $user      = $request->user();
        $isStation = isset($user->id_station_owner);

        $promotableType = $isStation ? 'App\Models\Station' : 'App\Models\Garage';

        if ($isStation && !$this->ownsStation($user->id_station_owner, $entityId)) {
            return response()->json(['success' => false, 'message' => 'Entité introuvable.'], 404);
        }

        if (!$isStation && !$this->ownsGarage($user->id_gara_owner, $entityId)) {
            return response()->json(['success' => false, 'message' => 'Entité introuvable.'], 404);
        }

        $promo = DB::table('promotions')
            ->where('id_promotion', $id)
            ->where('promotable_type', $promotableType)
            ->where('promotable_id', $entityId)
            ->whereNull('deleted_at')
            ->first();

        if (!$promo) {
            return response()->json(['success' => false, 'message' => 'Promotion introuvable.'], 404);
        }

        $newStatus = !$promo->is_active;

        DB::table('promotions')
            ->where('id_promotion', $id)
            ->update(['is_active' => $newStatus, 'updated_at' => now()]);

        return response()->json([
            'success'   => true,
            'message'   => $newStatus ? 'Promotion activée.' : 'Promotion désactivée.',
            'data'      => ['is_active' => $newStatus],
        ]);
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────
    private function validatePromotion(Request $request, bool $partial = false): array
    {
        $rule = $partial ? 'sometimes' : 'required';

        return $request->validate([
            'title'                   => "{$rule}|string|max:150",
            'description'             => 'nullable|string|max:1000',
            'type'                    => "{$rule}|in:discount,offre_speciale,service_gratuit,cadeau,autre",
            'discount_percent'        => 'nullable|numeric|min:0|max:100',
            'discount_amount'         => 'nullable|numeric|min:0',
            'starts_at'               => "{$rule}|date",
            'ends_at'                 => "{$rule}|date|after_or_equal:starts_at",
            'send_push_notification'  => 'sometimes|boolean',
            'notification_radius_km'  => 'sometimes|integer|min:1|max:50',
        ]);
    }

    private function ownsStation(int $ownerId, int $stationId): bool
    {
        return DB::table('station_owner_station')
            ->where('station_owner_id', $ownerId)
            ->where('station_id', $stationId)
            ->exists();
    }

    private function ownsGarage(int $ownerId, int $garageId): bool
    {
        return DB::table('garage_owner_garage')
            ->where('garage_owner_id', $ownerId)
            ->where('garage_id', $garageId)
            ->exists();
    }

    private function requireProPlanStation(Request $request): void
    {
        $sub = DB::table('subscriptions')
            ->where('subscribable_type', 'App\Models\StationOwner')
            ->where('subscribable_id', $request->user()->id_station_owner)
            ->where('status', 'active')
            ->whereIn('plan', ['station_pro', 'station_premium'])
            ->where('expires_at', '>=', now()->toDateString())
            ->first();

        if (!$sub) abort(response()->json(['success' => false, 'message' => 'Plan Pro/Premium requis.', 'upgrade' => true], 403));
    }

    private function requireProPlanGarage(Request $request): void
    {
        $sub = DB::table('subscriptions')
            ->where('subscribable_type', 'App\Models\GarageOwner')
            ->where('subscribable_id', $request->user()->id_gara_owner)
            ->where('status', 'active')
            ->whereIn('plan', ['garage_pro', 'garage_premium'])
            ->where('expires_at', '>=', now()->toDateString())
            ->first();

        if (!$sub) abort(response()->json(['success' => false, 'message' => 'Plan Pro/Premium requis.', 'upgrade' => true], 403));
    }
}
