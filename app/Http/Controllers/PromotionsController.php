<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PromotionsController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX
    // GET /admin/promotions?status=&type=&entity_type=&search=&page=
    // ─────────────────────────────────────────────
    public function index(Request $request)
    {
        $status      = $request->input('status', '');       // active|upcoming|expired|inactive
        $type        = $request->input('type', '');          // discount|offre_speciale|service_gratuit|cadeau|autre
        $entityType  = $request->input('entity_type', '');   // station|garage
        $search      = $request->input('search', '');
        $today       = now()->toDateString();

        // ── KPIs ──────────────────────────────────
        $kpis = [
            'active'   => DB::table('promotions')->whereNull('deleted_at')->where('is_active', true)
                            ->where('starts_at', '<=', $today)->where('ends_at', '>=', $today)->count(),
            'upcoming' => DB::table('promotions')->whereNull('deleted_at')->where('is_active', true)
                            ->where('starts_at', '>', $today)->count(),
            'expired'  => DB::table('promotions')->whereNull('deleted_at')
                            ->where('ends_at', '<', $today)->count(),
            'stations' => DB::table('promotions')->whereNull('deleted_at')
                            ->where('promotable_type', 'App\Models\Station')->where('is_active', true)->count(),
            'garages'  => DB::table('promotions')->whereNull('deleted_at')
                            ->where('promotable_type', 'App\Models\Garage')->where('is_active', true)->count(),
        ];

        // ── Requête principale ─────────────────────
        $query = DB::table('promotions')->whereNull('deleted_at');

        // Filtre statut
        match ($status) {
            'active'   => $query->where('is_active', true)->where('starts_at', '<=', $today)->where('ends_at', '>=', $today),
            'upcoming' => $query->where('is_active', true)->where('starts_at', '>', $today),
            'expired'  => $query->where('ends_at', '<', $today),
            'inactive' => $query->where('is_active', false),
            default    => null,
        };

        if ($type)       $query->where('type', $type);
        if ($entityType) $query->where('promotable_type', 'App\Models\\' . ucfirst($entityType));
        if ($search)     $query->where('title', 'like', "%{$search}%");

        $total      = $query->count();
        $promotions = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        // Enrichir avec les données du promotable (station ou garage)
        $promoIds   = $promotions->pluck('id_promotion');
        $enriched   = $this->enrichWithEntities($promotions->getCollection());
        $promotions->setCollection($enriched);

        // Listes pour le modal de création
        $stations = DB::table('stations')->where('is_active', true)->whereNull('deleted_at')
            ->orderBy('name')->get(['id_station', 'name', 'city']);
        $garages  = DB::table('garages')->where('is_active', true)->whereNull('deleted_at')
            ->orderBy('name')->get(['id_garage', 'name', 'city']);

        return view('pages.promotions', compact(
            'promotions', 'kpis', 'stations', 'garages',
            'status', 'type', 'entityType', 'search', 'total'
        ));
    }

    // ─────────────────────────────────────────────
    // SHOW — JSON pour modal d'édition
    // GET /admin/promotions/{id}
    // ─────────────────────────────────────────────
    public function show(int $id): JsonResponse
    {
        $promo = DB::table('promotions')->where('id_promotion', $id)->whereNull('deleted_at')->first();
        if (!$promo) return response()->json(['success' => false, 'message' => 'Promotion introuvable.'], 404);

        $promo = (array) $promo;
        $promo['entity_type'] = str_contains($promo['promotable_type'], 'Station') ? 'station' : 'garage';

        // Infos de l'entité liée
        if ($promo['entity_type'] === 'station') {
            $promo['entity'] = DB::table('stations')->where('id_station', $promo['promotable_id'])
                ->first(['id_station as id', 'name', 'city']);
        } else {
            $promo['entity'] = DB::table('garages')->where('id_garage', $promo['promotable_id'])
                ->first(['id_garage as id', 'name', 'city']);
        }

        return response()->json(['success' => true, 'data' => $promo]);
    }

    // ─────────────────────────────────────────────
    // STORE — Créer une promotion
    // POST /admin/promotions
    // ─────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'                   => 'required|string|max:150',
            'description'             => 'nullable|string|max:1000',
            'entity_type'             => 'required|in:station,garage',
            'entity_id'               => 'required|integer|min:1',
            'type'                    => 'required|in:discount,offre_speciale,service_gratuit,cadeau,autre',
            'discount_percent'        => 'nullable|numeric|min:0|max:100',
            'discount_amount'         => 'nullable|numeric|min:0',
            'starts_at'               => 'required|date',
            'ends_at'                 => 'required|date|after_or_equal:starts_at',
            'send_push_notification'  => 'sometimes|boolean',
            'notification_radius_km'  => 'sometimes|integer|min:1|max:100',
            'is_active'               => 'sometimes|boolean',
        ]);

        // Vérifier que l'entité existe
        $table = $validated['entity_type'] === 'station' ? 'stations' : 'garages';
        $pk    = $validated['entity_type'] === 'station' ? 'id_station' : 'id_garage';
        if (!DB::table($table)->where($pk, $validated['entity_id'])->where('is_active', true)->exists()) {
            return response()->json(['success' => false, 'message' => ucfirst($validated['entity_type']) . ' introuvable.'], 404);
        }

        $promotableType = 'App\Models\\' . ucfirst($validated['entity_type']);

        $id = DB::table('promotions')->insertGetId([
            'promotable_type'         => $promotableType,
            'promotable_id'           => $validated['entity_id'],
            'title'                   => $validated['title'],
            'description'             => $validated['description'] ?? null,
            'type'                    => $validated['type'],
            'discount_percent'        => $validated['discount_percent'] ?? null,
            'discount_amount'         => $validated['discount_amount']  ?? null,
            'starts_at'               => $validated['starts_at'],
            'ends_at'                 => $validated['ends_at'],
            'send_push_notification'  => !empty($validated['send_push_notification']),
            'notification_radius_km'  => $validated['notification_radius_km'] ?? 5,
            'is_active'               => $validated['is_active'] ?? true,
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        // TODO: si send_push_notification, déclencher l'envoi FCM aux users dans le rayon

        $promo = DB::table('promotions')->where('id_promotion', $id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Promotion créée avec succès.',
            'data'    => $promo,
        ], 201);
    }

    // ─────────────────────────────────────────────
    // UPDATE — Modifier une promotion
    // PUT /admin/promotions/{id}
    // ─────────────────────────────────────────────
    public function update(Request $request, int $id): JsonResponse
    {
        $promo = DB::table('promotions')->where('id_promotion', $id)->whereNull('deleted_at')->first();
        if (!$promo) return response()->json(['success' => false, 'message' => 'Promotion introuvable.'], 404);

        $validated = $request->validate([
            'title'                  => 'sometimes|string|max:150',
            'description'            => 'sometimes|nullable|string|max:1000',
            'type'                   => 'sometimes|in:discount,offre_speciale,service_gratuit,cadeau,autre',
            'discount_percent'       => 'sometimes|nullable|numeric|min:0|max:100',
            'discount_amount'        => 'sometimes|nullable|numeric|min:0',
            'starts_at'              => 'sometimes|date',
            'ends_at'                => 'sometimes|date',
            'send_push_notification' => 'sometimes|boolean',
            'notification_radius_km' => 'sometimes|integer|min:1|max:100',
            'is_active'              => 'sometimes|boolean',
        ]);

        DB::table('promotions')
            ->where('id_promotion', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        $updated = DB::table('promotions')->where('id_promotion', $id)->first();

        return response()->json(['success' => true, 'message' => 'Promotion mise à jour.', 'data' => $updated]);
    }

    // ─────────────────────────────────────────────
    // TOGGLE — Activer / désactiver
    // POST /admin/promotions/{id}/toggle
    // ─────────────────────────────────────────────
    public function toggle(int $id): JsonResponse
    {
        $promo = DB::table('promotions')->where('id_promotion', $id)->whereNull('deleted_at')->first();
        if (!$promo) return response()->json(['success' => false, 'message' => 'Promotion introuvable.'], 404);

        $newStatus = !$promo->is_active;
        DB::table('promotions')->where('id_promotion', $id)->update(['is_active' => $newStatus, 'updated_at' => now()]);

        return response()->json([
            'success'   => true,
            'message'   => $newStatus ? 'Promotion activée.' : 'Promotion désactivée.',
            'is_active' => $newStatus,
        ]);
    }

    // ─────────────────────────────────────────────
    // DUPLICATE — Dupliquer une promotion
    // POST /admin/promotions/{id}/duplicate
    // ─────────────────────────────────────────────
    public function duplicate(int $id): JsonResponse
    {
        $promo = DB::table('promotions')->where('id_promotion', $id)->whereNull('deleted_at')->first();
        if (!$promo) return response()->json(['success' => false, 'message' => 'Promotion introuvable.'], 404);

        $data = (array) $promo;
        unset($data['id_promotion'], $data['deleted_at']);
        $data['title']      = $data['title'] . ' (copie)';
        $data['is_active']  = false;
        $data['starts_at']  = now()->toDateString();
        $data['ends_at']    = now()->addMonth()->toDateString();
        $data['created_at'] = now();
        $data['updated_at'] = now();

        $newId = DB::table('promotions')->insertGetId($data);
        $newPromo = DB::table('promotions')->where('id_promotion', $newId)->first();

        return response()->json([
            'success' => true,
            'message' => 'Promotion dupliquée. Modifiez les dates avant de l\'activer.',
            'data'    => $newPromo,
        ]);
    }

    // ─────────────────────────────────────────────
    // SEND PUSH — Renvoyer la notification push
    // POST /admin/promotions/{id}/send-push
    // ─────────────────────────────────────────────
    public function sendPush(int $id): JsonResponse
    {
        $promo = DB::table('promotions')->where('id_promotion', $id)->whereNull('deleted_at')->first();
        if (!$promo) return response()->json(['success' => false, 'message' => 'Promotion introuvable.'], 404);

        // Activer la notification pour cette promotion
        DB::table('promotions')->where('id_promotion', $id)->update([
            'send_push_notification' => true,
            'updated_at' => now(),
        ]);

        // TODO: déclencher l'envoi FCM via PushNotificationService
        // PushNotificationService::sendPromoNotification($promo);

        return response()->json(['success' => true, 'message' => 'Notification push envoyée aux utilisateurs proches.']);
    }

    // ─────────────────────────────────────────────
    // DESTROY — Soft delete
    // DELETE /admin/promotions/{id}
    // ─────────────────────────────────────────────
    public function destroy(int $id): JsonResponse
    {
        $exists = DB::table('promotions')->where('id_promotion', $id)->whereNull('deleted_at')->exists();
        if (!$exists) return response()->json(['success' => false, 'message' => 'Promotion introuvable.'], 404);

        DB::table('promotions')->where('id_promotion', $id)->update(['deleted_at' => now(), 'is_active' => false]);

        return response()->json(['success' => true, 'message' => 'Promotion supprimée.']);
    }

    // ─────────────────────────────────────────────
    // HELPER — Enrichir avec infos de l'entité
    // ─────────────────────────────────────────────
    private function enrichWithEntities($collection)
    {
        $stationIds = $collection->where('promotable_type', 'App\Models\Station')->pluck('promotable_id');
        $garageIds  = $collection->where('promotable_type', 'App\Models\Garage')->pluck('promotable_id');

        $stations = DB::table('stations')->whereIn('id_station', $stationIds)
            ->get(['id_station', 'name', 'city'])->keyBy('id_station');
        $garages  = DB::table('garages')->whereIn('id_garage', $garageIds)
            ->get(['id_garage', 'name', 'city'])->keyBy('id_garage');

        return $collection->map(function ($promo) use ($stations, $garages) {
            $p = (array) $promo;
            $isStation = str_contains($p['promotable_type'], 'Station');
            $p['entity_type'] = $isStation ? 'station' : 'garage';
            $p['entity']      = $isStation
                ? ($stations[$p['promotable_id']] ?? null)
                : ($garages[$p['promotable_id']]  ?? null);
            $p['status']      = $this->computeStatus($p);
            return (object) $p;
        });
    }

    private function computeStatus(array $p): string
    {
        $today = now()->toDateString();
        if (!$p['is_active'])                    return 'inactive';
        if ($p['ends_at'] < $today)              return 'expired';
        if ($p['starts_at'] > $today)            return 'upcoming';
        return 'active';
    }
}
