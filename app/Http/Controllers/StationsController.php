<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StationsController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX
    // GET /admin/stations?search=&plan=&city=&status=&verified=&page=
    // ─────────────────────────────────────────────
    public function index(Request $request)
    {
        $search   = $request->input('search');
        $plan     = $request->input('plan');
        $city     = $request->input('city');
        $status   = $request->input('status');
        $verified = $request->boolean('verified');
        $limit    = 20;

        // ── KPIs ──────────────────────────────────
        $kpis = [
            'total'    => DB::table('stations')->whereNull('deleted_at')->count(),
            'verified' => DB::table('stations')->whereNull('deleted_at')->where('is_verified', true)->count(),
            'pro'      => DB::table('stations')->whereNull('deleted_at')->whereIn('subscription_type', ['pro', 'premium'])->count(),
            'inactive' => DB::table('stations')->whereNull('deleted_at')->where('is_active', false)->count(),
        ];

        // ── Requête principale ─────────────────────
        $query = DB::table('stations')->whereNull('deleted_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }
        if ($plan)              $query->where('subscription_type', $plan);
        if ($city)              $query->where('city', $city);
        if ($status === 'active')   $query->where('is_active', true);
        if ($status === 'inactive') $query->where('is_active', false);
        if ($verified)          $query->where('is_verified', true);

        $total    = $query->count();
        $stations = $query->orderByDesc('views_count')->paginate($limit)->withQueryString();

        // Prix essence + gasoil en une requête
        $stationIds = $stations->pluck('id_station');

        $prices = DB::table('fuel_prices')
            ->whereIn('station_id', $stationIds)
            ->whereIn('fuel_type', ['essence', 'gasoil'])
            ->where('is_available', true)
            ->get()
            ->groupBy('station_id');

        // Villes distinctes pour filtre
        $cities = DB::table('stations')
            ->whereNull('deleted_at')
            ->whereNotNull('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return view('pages.stations', compact(
            'stations', 'kpis', 'prices', 'cities',
            'search', 'plan', 'city', 'status', 'verified', 'total'
        ));
    }

    // ─────────────────────────────────────────────
    // SHOW — Détail complet (JSON pour modal)
    // GET /admin/stations/{id}
    // ─────────────────────────────────────────────
    public function show(int $id): JsonResponse
    {
        $station = DB::table('stations')
            ->where('id_station', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$station) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $prices   = DB::table('fuel_prices')->where('station_id', $id)->orderBy('fuel_type')->get();
        $services = DB::table('station_services')->where('station_id', $id)->pluck('service');

        $monthStart = now()->startOfMonth();
        $viewStats  = DB::table('station_views')
            ->where('station_id', $id)
            ->where('viewed_at', '>=', $monthStart)
            ->selectRaw("action, COUNT(*) as count")
            ->groupBy('action')
            ->get()
            ->keyBy('action');

        $lastPriceUpdate = DB::table('fuel_price_history')
            ->where('station_id', $id)
            ->orderByDesc('changed_at')
            ->value('changed_at');

        $activeSub = DB::table('subscriptions')
            ->where('subscribable_type', 'App\Models\Station')
            ->where('subscribable_id', $id)
            ->where('status', 'active')
            ->orderByDesc('expires_at')
            ->first(['plan', 'expires_at', 'billing_cycle']);

        $avgRating = DB::table('reviews')
            ->where('reviewable_type', 'App\Models\Station')
            ->where('reviewable_id', $id)
            ->where('is_approved', true)
            ->whereNull('deleted_at')
            ->selectRaw('AVG(rating) as avg, COUNT(*) as total')
            ->first();

        return response()->json([
            'success' => true,
            'data'    => array_merge((array) $station, [
                'prices'           => $prices,
                'services'         => $services,
                'stats_month'      => $viewStats,
                'last_price_update'=> $lastPriceUpdate,
                'subscription'     => $activeSub,
                'rating'           => [
                    'avg'   => round($avgRating->avg ?? 0, 1),
                    'total' => $avgRating->total ?? 0,
                ],
            ]),
        ]);
    }

    // ─────────────────────────────────────────────
    // STORE — Créer une station
    // POST /admin/stations
    // ─────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:150',
            'brand'             => 'nullable|string|max:100',
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
            // Prix carburant (optionnels)
            'price_essence'     => 'nullable|numeric|min:0',
            'price_gasoil'      => 'nullable|numeric|min:0',
            'price_sans_plomb'  => 'nullable|numeric|min:0',
        ]);

        $stationId = DB::table('stations')->insertGetId([
            'name'              => $validated['name'],
            'brand'             => $validated['brand'] ?? null,
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
            'is_active'         => true,
            'is_verified'       => false,
            'views_count'       => 0,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // Insérer les prix carburant fournis
        $fuelMap = [
            'essence'    => $validated['price_essence']    ?? null,
            'gasoil'     => $validated['price_gasoil']     ?? null,
            'sans_plomb' => $validated['price_sans_plomb'] ?? null,
        ];

        foreach ($fuelMap as $type => $price) {
            if ($price !== null) {
                DB::table('fuel_prices')->insert([
                    'station_id'       => $stationId,
                    'fuel_type'        => $type,
                    'price'            => $price,
                    'is_available'     => true,
                    'updated_at_price' => now(),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        }

        $station = DB::table('stations')->where('id_station', $stationId)->first();

        return response()->json([
            'success' => true,
            'message' => 'Station créée avec succès.',
            'data'    => $station,
        ], 201);
    }

    // ─────────────────────────────────────────────
    // UPDATE PRICES — Mettre à jour les prix d'une station
    // POST /admin/stations/{id}/prices
    // Body: { prices: [{ fuel_type, price, is_available }] }
    // ─────────────────────────────────────────────
    public function updatePrices(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'prices'                => 'required|array|min:1',
            'prices.*.fuel_type'    => 'required|in:essence,gasoil,sans_plomb,super,gpl',
            'prices.*.price'        => 'required|numeric|min:0',
            'prices.*.is_available' => 'sometimes|boolean',
        ]);

        $station = DB::table('stations')->where('id_station', $id)->whereNull('deleted_at')->first();
        if (!$station) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $historyRows = [];

        foreach ($request->prices as $item) {
            $fuelType = $item['fuel_type'];
            $newPrice = (float) $item['price'];

            $existing = DB::table('fuel_prices')
                ->where('station_id', $id)
                ->where('fuel_type', $fuelType)
                ->first();

            DB::table('fuel_prices')->updateOrInsert(
                ['station_id' => $id, 'fuel_type' => $fuelType],
                [
                    'price'            => $newPrice,
                    'is_available'     => $item['is_available'] ?? true,
                    'updated_at_price' => now(),
                    'updated_at'       => now(),
                    'created_at'       => now(),
                ]
            );

            if ($existing && (float) $existing->price !== $newPrice) {
                $historyRows[] = [
                    'station_id'      => $id,
                    'fuel_type'       => $fuelType,
                    'old_price'       => $existing->price,
                    'new_price'       => $newPrice,
                    'changed_by_type' => 'admin',
                    'changed_by_id'   => auth()->id(),
                    'changed_at'      => now(),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }
        }

        if (!empty($historyRows)) {
            DB::table('fuel_price_history')->insert($historyRows);
        }

        $prices = DB::table('fuel_prices')->where('station_id', $id)->orderBy('fuel_type')->get();

        return response()->json(['success' => true, 'message' => 'Prix mis à jour.', 'data' => $prices]);
    }

    // ─────────────────────────────────────────────
    // VERIFY — Attribuer / retirer le badge vérifié
    // POST /admin/stations/{id}/verify
    // ─────────────────────────────────────────────
    public function verify(int $id): JsonResponse
    {
        $station = DB::table('stations')->where('id_station', $id)->whereNull('deleted_at')->first();
        if (!$station) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $newStatus = !$station->is_verified;
        DB::table('stations')->where('id_station', $id)->update([
            'is_verified' => $newStatus,
            'updated_at'  => now(),
        ]);

        return response()->json([
            'success'     => true,
            'message'     => $newStatus ? 'Badge vérifié attribué.' : 'Badge vérifié retiré.',
            'is_verified' => $newStatus,
        ]);
    }

    // ─────────────────────────────────────────────
    // TOGGLE — Activer / désactiver
    // POST /admin/stations/{id}/toggle
    // ─────────────────────────────────────────────
    public function toggle(int $id): JsonResponse
    {
        $station = DB::table('stations')->where('id_station', $id)->whereNull('deleted_at')->first();
        if (!$station) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $newStatus = !$station->is_active;
        DB::table('stations')->where('id_station', $id)->update([
            'is_active'  => $newStatus,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success'   => true,
            'message'   => $newStatus ? 'Station activée.' : 'Station désactivée.',
            'is_active' => $newStatus,
        ]);
    }

    // ─────────────────────────────────────────────
    // DESTROY — Soft delete
    // DELETE /admin/stations/{id}
    // ─────────────────────────────────────────────
    public function destroy(int $id): JsonResponse
    {
        $exists = DB::table('stations')->where('id_station', $id)->whereNull('deleted_at')->exists();
        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        DB::table('stations')->where('id_station', $id)->update([
            'deleted_at' => now(),
            'is_active'  => false,
        ]);

        return response()->json(['success' => true, 'message' => 'Station supprimée.']);
    }

    // ─────────────────────────────────────────────
    // EXPORT CSV
    // GET /admin/stations/export
    // ─────────────────────────────────────────────
    public function export(Request $request)
    {
        $query = DB::table('stations')->whereNull('deleted_at')
            ->select('id_station', 'name', 'brand', 'city', 'address', 'phone',
                     'subscription_type', 'is_verified', 'is_active', 'views_count', 'created_at');

        if ($request->filled('plan'))   $query->where('subscription_type', $request->plan);
        if ($request->filled('city'))   $query->where('city', $request->city);
        if ($request->boolean('verified')) $query->where('is_verified', true);

        $rows = $query->orderByDesc('created_at')->get();

        $headers = ['ID', 'Nom', 'Marque', 'Ville', 'Adresse', 'Téléphone', 'Plan', 'Vérifiée', 'Active', 'Vues', 'Créée le'];
        $csv = collect([$headers])->merge($rows->map(fn ($r) => [
            $r->id_station, $r->name, $r->brand ?? '', $r->city, $r->address,
            $r->phone ?? '', $r->subscription_type,
            $r->is_verified ? 'Oui' : 'Non',
            $r->is_active   ? 'Oui' : 'Non',
            $r->views_count, $r->created_at,
        ]))->map(fn ($row) => implode(';', array_map(fn ($v) => '"' . str_replace('"', '""', $v) . '"', $row)))->implode("\n");

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="stations_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
