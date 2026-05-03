<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class GaragesController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX
    // GET /admin/garages?search=&type=&plan=&city=&status=&verified=&page=
    // ─────────────────────────────────────────────
    public function index(Request $request)
    {
        $search   = $request->input('search', '');
        $type     = $request->input('type', '');
        $plan     = $request->input('plan', '');
        $city     = $request->input('city', '');
        $status   = $request->input('status', '');
        $verified = $request->boolean('verified');
        $limit    = 20;

        // ── KPIs ──────────────────────────────────
        $kpis = [
            'total'      => DB::table('garages')->whereNull('deleted_at')->count(),
            'verified'   => DB::table('garages')->whereNull('deleted_at')->where('is_verified', true)->count(),
            'pro'        => DB::table('garages')->whereNull('deleted_at')->whereIn('subscription_type', ['pro', 'premium'])->count(),
            'avg_rating' => round(DB::table('garages')->whereNull('deleted_at')->where('is_active', true)->avg('rating') ?? 0, 1),
            'inactive'   => DB::table('garages')->whereNull('deleted_at')->where('is_active', false)->count(),
        ];

        // ── Répartition par type ───────────────────
        $byType = DB::table('garages')
            ->whereNull('deleted_at')
            ->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->orderByDesc('count')
            ->get()
            ->keyBy('type');

        // ── Requête principale ─────────────────────
        $query = DB::table('garages')->whereNull('deleted_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }
        if ($type)              $query->where('type', $type);
        if ($plan)              $query->where('subscription_type', $plan);
        if ($city)              $query->where('city', $city);
        if ($status === 'active')   $query->where('is_active', true);
        if ($status === 'inactive') $query->where('is_active', false);
        if ($verified)          $query->where('is_verified', true);

        $total   = $query->count();
        $garages = $query->orderByDesc('views_count')->paginate($limit)->withQueryString();

        // Villes distinctes pour filtre
        $cities = DB::table('garages')
            ->whereNull('deleted_at')
            ->whereNotNull('city')
            ->distinct()->orderBy('city')->pluck('city');

        return view('pages.garages', compact(
            'garages', 'kpis', 'byType', 'cities',
            'search', 'type', 'plan', 'city', 'status', 'verified', 'total'
        ));
    }

    // ─────────────────────────────────────────────
    // SHOW — Détail complet (JSON pour modal)
    // GET /admin/garages/{id}
    // ─────────────────────────────────────────────
    public function show(int $id): JsonResponse
    {
        $garage = DB::table('garages')->where('id_garage', $id)->whereNull('deleted_at')->first();

        if (!$garage) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
        }

        $services = DB::table('garage_services')->where('garage_id', $id)->get();

        $monthStart = now()->startOfMonth()->toDateTimeString();
        $viewStats  = DB::table('garage_views')
            ->where('garage_id', $id)
            ->where('viewed_at', '>=', $monthStart)
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->get()->keyBy('action');

        $recentReviews = DB::table('reviews')
            ->join('users_carbur', 'users_carbur.id_user_carbu', '=', 'reviews.user_id')
            ->where('reviews.reviewable_type', 'App\Models\Garage')
            ->where('reviews.reviewable_id', $id)
            ->whereNull('reviews.deleted_at')
            ->orderByDesc('reviews.created_at')
            ->limit(5)
            ->get(['reviews.id_review', 'reviews.rating', 'reviews.comment',
                   'reviews.is_approved', 'reviews.created_at', 'users_carbur.name as user_name']);

        $activeSub = DB::table('subscriptions')
            ->where('subscribable_type', 'App\Models\Garage')
            ->where('subscribable_id', $id)
            ->where('status', 'active')
            ->orderByDesc('expires_at')
            ->first(['plan', 'expires_at', 'billing_cycle']);

        return response()->json([
            'success' => true,
            'data'    => array_merge((array) $garage, [
                'services'      => $services,
                'stats_month'   => $viewStats,
                'recent_reviews'=> $recentReviews,
                'subscription'  => $activeSub,
            ]),
        ]);
    }

    // ─────────────────────────────────────────────
    // STORE — Créer un garage
    // POST /admin/garages
    // ─────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:150',
            'type'              => 'required|in:garage_general,centre_vidange,lavage_auto,pneus,batterie,climatisation,electricite_auto,depannage,carrosserie,vitrage',
            'address'           => 'required|string|max:255',
            'city'              => 'required|string|max:100',
            'latitude'          => 'required|numeric|between:-90,90',
            'longitude'         => 'required|numeric|between:-180,180',
            'phone'             => 'nullable|string|max:20',
            'whatsapp'          => 'nullable|string|max:20',
            'opens_at'          => 'nullable|date_format:H:i',
            'closes_at'         => 'nullable|date_format:H:i',
            'is_open_24h'       => 'sometimes|boolean',
            'subscription_type' => 'required|in:free,pro,premium',
            'description'       => 'nullable|string|max:2000',
            'services'          => 'nullable|array',
            'services.*'        => 'string',
        ]);

        $garageId = DB::table('garages')->insertGetId([
            'name'              => $validated['name'],
            'type'              => $validated['type'],
            'address'           => $validated['address'],
            'city'              => $validated['city'],
            'country'           => 'CI',
            'latitude'          => $validated['latitude'],
            'longitude'         => $validated['longitude'],
            'phone'             => $validated['phone'] ?? null,
            'whatsapp'          => $validated['whatsapp'] ?? null,
            'opens_at'          => !empty($validated['is_open_24h']) ? null : ($validated['opens_at'] ?? null),
            'closes_at'         => !empty($validated['is_open_24h']) ? null : ($validated['closes_at'] ?? null),
            'is_open_24h'       => !empty($validated['is_open_24h']),
            'subscription_type' => $validated['subscription_type'],
            'description'       => $validated['description'] ?? null,
            'is_active'         => true,
            'is_verified'       => false,
            'views_count'       => 0,
            'rating'            => 0,
            'rating_count'      => 0,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // Insérer les services sélectionnés
        if (!empty($validated['services'])) {
            $rows = array_map(fn ($svc) => [
                'garage_id'  => $garageId,
                'service'    => $svc,
                'created_at' => now(),
                'updated_at' => now(),
            ], array_unique($validated['services']));
            DB::table('garage_services')->insert($rows);
        }

        $garage = DB::table('garages')->where('id_garage', $garageId)->first();

        return response()->json(['success' => true, 'message' => 'Garage créé avec succès.', 'data' => $garage], 201);
    }

    // ─────────────────────────────────────────────
    // UPDATE SERVICES — Gérer les services d'un garage
    // POST /admin/garages/{id}/services
    // Body: { services: [{ service: "vidange", price_range: "15000-25000 FCFA" }] }
    // ─────────────────────────────────────────────
    public function updateServices(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'services'               => 'required|array',
            'services.*.service'     => 'required|string',
            'services.*.price_range' => 'nullable|string|max:100',
        ]);

        $exists = DB::table('garages')->where('id_garage', $id)->whereNull('deleted_at')->exists();
        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
        }

        DB::table('garage_services')->where('garage_id', $id)->delete();

        $rows = array_map(fn ($svc) => [
            'garage_id'   => $id,
            'service'     => $svc['service'],
            'price_range' => $svc['price_range'] ?? null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ], $request->services);

        DB::table('garage_services')->insert($rows);

        $services = DB::table('garage_services')->where('garage_id', $id)->get();

        return response()->json(['success' => true, 'message' => 'Services mis à jour.', 'data' => $services]);
    }

    // ─────────────────────────────────────────────
    // VERIFY — Toggle badge vérifié
    // POST /admin/garages/{id}/verify
    // ─────────────────────────────────────────────
    public function verify(int $id): JsonResponse
    {
        $garage = DB::table('garages')->where('id_garage', $id)->whereNull('deleted_at')->first();
        if (!$garage) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
        }

        $newStatus = !$garage->is_verified;
        DB::table('garages')->where('id_garage', $id)->update(['is_verified' => $newStatus, 'updated_at' => now()]);

        return response()->json([
            'success'     => true,
            'message'     => $newStatus ? 'Badge vérifié attribué.' : 'Badge vérifié retiré.',
            'is_verified' => $newStatus,
        ]);
    }

    // ─────────────────────────────────────────────
    // TOGGLE — Activer / désactiver
    // POST /admin/garages/{id}/toggle
    // ─────────────────────────────────────────────
    public function toggle(int $id): JsonResponse
    {
        $garage = DB::table('garages')->where('id_garage', $id)->whereNull('deleted_at')->first();
        if (!$garage) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
        }

        $newStatus = !$garage->is_active;
        DB::table('garages')->where('id_garage', $id)->update(['is_active' => $newStatus, 'updated_at' => now()]);

        return response()->json([
            'success'   => true,
            'message'   => $newStatus ? 'Garage activé.' : 'Garage désactivé.',
            'is_active' => $newStatus,
        ]);
    }

    // ─────────────────────────────────────────────
    // DESTROY — Soft delete
    // DELETE /admin/garages/{id}
    // ─────────────────────────────────────────────
    public function destroy(int $id): JsonResponse
    {
        $exists = DB::table('garages')->where('id_garage', $id)->whereNull('deleted_at')->exists();
        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
        }

        DB::table('garages')->where('id_garage', $id)->update(['deleted_at' => now(), 'is_active' => false]);

        return response()->json(['success' => true, 'message' => 'Garage supprimé.']);
    }

    // ─────────────────────────────────────────────
    // EXPORT CSV
    // GET /admin/garages/export
    // ─────────────────────────────────────────────
    public function export(Request $request)
    {
        $query = DB::table('garages')->whereNull('deleted_at')
            ->select('id_garage', 'name', 'type', 'city', 'address', 'phone',
                     'subscription_type', 'is_verified', 'is_active', 'rating', 'rating_count', 'views_count', 'created_at');

        if ($request->filled('type'))   $query->where('type', $request->type);
        if ($request->filled('plan'))   $query->where('subscription_type', $request->plan);
        if ($request->filled('city'))   $query->where('city', $request->city);
        if ($request->boolean('verified')) $query->where('is_verified', true);

        $rows    = $query->orderByDesc('created_at')->get();
        $headers = ['ID', 'Nom', 'Type', 'Ville', 'Adresse', 'Téléphone', 'Plan', 'Vérifié', 'Actif', 'Note', 'Nb avis', 'Vues', 'Créé le'];

        $csv = collect([$headers])->merge($rows->map(fn ($r) => [
            $r->id_garage, $r->name, $r->type, $r->city, $r->address, $r->phone ?? '',
            $r->subscription_type, $r->is_verified ? 'Oui' : 'Non', $r->is_active ? 'Oui' : 'Non',
            $r->rating, $r->rating_count, $r->views_count, $r->created_at,
        ]))->map(fn ($row) => implode(';', array_map(fn ($v) => '"' . str_replace('"', '""', (string)$v) . '"', $row)))->implode("\n");

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="garages_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
