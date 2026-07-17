<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FuelPrice;
use App\Models\Station;
use App\Models\StationOwner;
use App\Models\StationOwnerStation;
use App\Models\StationService;
use Illuminate\Http\Request;

class StationsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $plan = $request->get('plan', '');
        $city = $request->get('city', '');
        $status = $request->get('status', '');
        $verified = $request->boolean('verified');

        $query = Station::query()->with('owner');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }
        if ($plan) $query->where('subscription_type', $plan);
        if ($city) $query->where('city', $city);
        if ($status === 'active') $query->where('is_active', true);
        elseif ($status === 'inactive') $query->where('is_active', false);
        if ($verified) $query->where('is_verified', true);

        $stations = $query->orderByDesc('id_station')->paginate(15)->withQueryString();
        $total = Station::count();

        $stationIds = $stations->pluck('id_station');
        $prices = FuelPrice::whereIn('station_id', $stationIds)->get()->groupBy('station_id');

        $kpis = [
            'total' => Station::count(),
            'verified' => Station::where('is_verified', true)->count(),
            'pro' => Station::whereIn('subscription_type', ['pro', 'premium'])->count(),
            'inactive' => Station::where('is_active', false)->count(),
        ];

        $cities = Station::select('city')->distinct()->orderBy('city')->pluck('city');

        // Propriétaires approuvés, pour le select obligatoire du formulaire d'ajout
        $owners = StationOwner::where('status', 'approved')->orderBy('name')->get(['id_station_owner', 'name', 'company_name']);

        return view('pages.stations', compact(
            'stations',
            'prices',
            'kpis',
            'cities',
            'total',
            'owners',
            'search',
            'plan',
            'city',
            'status',
            'verified'
        ));
    }

    public function show(Station $station)
    {
        $station->load('services', 'fuelPrices', 'owner', 'team');

        return response()->json([
            'success' => true,
            'data' => [
                'id_station' => $station->id_station,
                'name' => $station->name,
                'city' => $station->city,
                'address' => $station->address,
                'phone' => $station->phone,
                'views_count' => $station->views_count,
                'subscription_type' => $station->subscription_type,
                'is_verified' => $station->is_verified,
                'is_active' => $station->is_active,
                'owner' => $station->owner ? [
                    'id_station_owner' => $station->owner->id_station_owner,
                    'name' => $station->owner->name,
                ] : null,
                'prices' => $station->fuelPrices->map(fn($p) => [
                    'fuel_type' => $p->fuel_type,
                    'price' => $p->price,
                    'is_available' => $p->is_available,
                ]),
                'services' => $station->services->pluck('service'),
                'team' => $station->team->map(fn($m) => [
                    'id_station_owner' => $m->id_station_owner,
                    'name' => $m->name,
                    'email' => $m->email,
                    'role' => $m->pivot->role,
                    'pivot_id' => $m->pivot->id_stat_owner_stat,
                ]),
            ],
        ]);
    }

    /**
     * Corrigé : owner_id est obligatoire (NOT NULL + FK) dans la migration,
     * il manquait totalement dans l'ancien formulaire. Le champ "brand" a été
     * retiré car il n'existe pas dans la table stations.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'nullable|string|max:3',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'opens_at' => 'nullable',
            'closes_at' => 'nullable',
            'is_open_24h' => 'boolean',
            'subscription_type' => 'in:free,pro,premium',
            'description' => 'nullable|string',
            'owner_id' => 'required|exists:station_owners,id_station_owner',
        ]);

        $station = Station::create($data);

        // Lie automatiquement le propriétaire dans l'équipe avec le rôle "owner"
        StationOwnerStation::create([
            'owner_id' => $data['owner_id'],
            'station_id' => $station->id_station,
            'role' => 'owner',
        ]);

        // Prix carburant optionnels saisis dans le formulaire rapide
        foreach (['essence', 'gasoil', 'sans_plomb', 'super', 'gpl'] as $type) {
            $key = 'price_' . $type;
            if ($request->filled($key)) {
                FuelPrice::create([
                    'station_id' => $station->id_station,
                    'fuel_type' => $type,
                    'price' => $request->input($key),
                    'is_available' => true,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Station ajoutée avec succès',
            'data' => $station,
        ]);
    }

    public function updatePrices(Request $request, Station $station)
    {
        $prices = $request->input('prices', []);

        foreach ($prices as $p) {
            FuelPrice::updateOrCreate(
                ['station_id' => $station->id_station, 'fuel_type' => $p['fuel_type']],
                [
                    'price' => $p['price'],
                    'is_available' => $p['is_available'] ?? true,
                    'updated_at_price' => now(),
                ]
            );
        }

        return response()->json(['success' => true, 'message' => 'Prix mis à jour']);
    }

    /**
     * Nouveau : gestion des services de la station (table station_services),
     * absente du formulaire d'origine — la station ne pouvait pas être éditée.
     */
    public function updateServices(Request $request, Station $station)
    {
        $selected = $request->input('services', []); // tableau de clés ex: ['wifi','parking']

        $station->services()->delete();

        foreach ($selected as $service) {
            StationService::create([
                'station_id' => $station->id_station,
                'service' => $service,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Services mis à jour']);
    }

    /**
     * Nouveau : gestion de l'équipe (table station_owner_station) — ajouter
     * un manager/employee existant (par email) à la station.
     */
    public function addTeamMember(Request $request, Station $station)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:station_owners,email',
            'role' => 'required|in:owner,manager,employee',
        ]);

        $owner = StationOwner::where('email', $data['email'])->first();

        $exists = StationOwnerStation::where('station_id', $station->id_station)
            ->where('owner_id', $owner->id_station_owner)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Ce membre est déjà rattaché à cette station']);
        }

        StationOwnerStation::create([
            'station_id' => $station->id_station,
            'owner_id' => $owner->id_station_owner,
            'role' => $data['role'],
        ]);

        return response()->json(['success' => true, 'message' => 'Membre ajouté à l\'équipe']);
    }

    public function removeTeamMember(Station $station, StationOwnerStation $member)
    {
        if ($member->station_id !== $station->id_station) {
            return response()->json(['success' => false, 'message' => 'Membre introuvable pour cette station'], 404);
        }

        if ($member->role === 'owner') {
            return response()->json(['success' => false, 'message' => 'Impossible de retirer le propriétaire principal']);
        }

        $member->delete();

        return response()->json(['success' => true, 'message' => 'Membre retiré de l\'équipe']);
    }

    public function verify(Station $station)
    {
        $station->is_verified = ! $station->is_verified;
        $station->save();

        return response()->json([
            'success' => true,
            'message' => $station->is_verified ? 'Station vérifiée' : 'Badge retiré',
            'is_verified' => $station->is_verified,
        ]);
    }

    public function toggle(Station $station)
    {
        $station->is_active = ! $station->is_active;
        $station->save();

        return response()->json([
            'success' => true,
            'message' => $station->is_active ? 'Station activée' : 'Station désactivée',
            'is_active' => $station->is_active,
        ]);
    }

    public function destroy(Station $station)
    {
        $station->delete();

        return response()->json(['success' => true, 'message' => 'Station supprimée']);
    }

    public function export(Request $request)
    {
        $stations = Station::with('owner')->get();
        $filename = 'stations_' . now()->format('Y-m-d') . '.csv';

        $callback = function () use ($stations) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Nom', 'Ville', 'Adresse', 'Téléphone', 'Plan', 'Propriétaire', 'Vérifiée', 'Active']);
            foreach ($stations as $s) {
                fputcsv($handle, [
                    $s->name,
                    $s->city,
                    $s->address,
                    $s->phone,
                    $s->subscription_type,
                    $s->owner?->name,
                    $s->is_verified ? 'Oui' : 'Non',
                    $s->is_active ? 'Oui' : 'Non',
                ]);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }
}
