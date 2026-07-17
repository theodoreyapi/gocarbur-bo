<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Station;
use App\Models\StationService;
use Illuminate\Http\Request;

class StationServicesController extends Controller
{
    public const SERVICES = [
        'lavage_auto', 'gonflage_pneus', 'boutique', 'restaurant', 'toilettes',
        'wifi', 'atm', 'parking', 'gonflage_gratuit', 'huile_moteur', 'reparation_rapide',
    ];

    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $service = $request->get('service', '');
        $stationId = $request->get('station_id', '');

        $query = StationService::query()->with('station');

        if ($search) {
            $query->whereHas('station', fn ($s) => $s->where('name', 'like', "%{$search}%"));
        }
        if ($service) {
            $query->where('service', $service);
        }
        if ($stationId) {
            $query->where('station_id', $stationId);
        }

        $services = $query->orderByDesc('id_sta_service')->paginate(15)->withQueryString();
        $total = StationService::count();

        $mostCommon = StationService::selectRaw('service, count(*) as total')
            ->groupBy('service')
            ->orderByDesc('total')
            ->first();

        $kpis = [
            'total' => $total,
            'stations_with_services' => StationService::distinct('station_id')->count('station_id'),
            'most_common' => $mostCommon?->service ?? '—',
            'distinct_types' => StationService::distinct('service')->count('service'),
        ];

        $allStations = Station::orderBy('name')->get(['id_station', 'name', 'city']);

        return view('pages.station-services', compact(
            'services', 'kpis', 'total', 'allStations',
            'search', 'service', 'stationId'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'station_id' => 'required|exists:stations,id_station',
            'service' => 'required|in:' . implode(',', self::SERVICES),
        ]);

        $exists = StationService::where('station_id', $data['station_id'])
            ->where('service', $data['service'])
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Ce service existe déjà pour cette station']);
        }

        $entry = StationService::create($data);

        return response()->json(['success' => true, 'message' => 'Service ajouté avec succès', 'data' => $entry]);
    }

    public function show(StationService $stationService)
    {
        $stationService->load('station');

        return response()->json(['success' => true, 'data' => $stationService]);
    }

    public function update(Request $request, StationService $stationService)
    {
        $data = $request->validate([
            'station_id' => 'required|exists:stations,id_station',
            'service' => 'required|in:' . implode(',', self::SERVICES),
        ]);

        $exists = StationService::where('station_id', $data['station_id'])
            ->where('service', $data['service'])
            ->where('id_sta_service', '!=', $stationService->id_sta_service)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Ce service existe déjà pour cette station']);
        }

        $stationService->update($data);

        return response()->json(['success' => true, 'message' => 'Service modifié avec succès']);
    }

    public function destroy(StationService $stationService)
    {
        $stationService->delete();

        return response()->json(['success' => true, 'message' => 'Service supprimé']);
    }
}
