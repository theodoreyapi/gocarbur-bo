<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Garage;
use App\Models\GarageService;
use Illuminate\Http\Request;

class GarageServicesController extends Controller
{
    public const SERVICES = GarageService::TYPES;

    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $service = $request->get('service', '');
        $garageId = $request->get('garage_id', '');

        $query = GarageService::query()->with('garage');

        if ($search) {
            $query->whereHas('garage', fn ($g) => $g->where('name', 'like', "%{$search}%"));
        }
        if ($service) {
            $query->where('service', $service);
        }
        if ($garageId) {
            $query->where('garage_id', $garageId);
        }

        $services = $query->orderByDesc('id_gara_service')->paginate(15)->withQueryString();
        $total = GarageService::count();

        $mostCommon = GarageService::selectRaw('service, count(*) as total')
            ->groupBy('service')
            ->orderByDesc('total')
            ->first();

        $kpis = [
            'total' => $total,
            'garages_with_services' => GarageService::distinct('garage_id')->count('garage_id'),
            'most_common' => $mostCommon?->service ?? '—',
            'distinct_types' => GarageService::distinct('service')->count('service'),
        ];

        $allGarages = Garage::orderBy('name')->get(['id_garage', 'name', 'city']);

        return view('pages.garage-services', compact(
            'services', 'kpis', 'total', 'allGarages',
            'search', 'service', 'garageId'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'garage_id' => 'required|exists:garages,id_garage',
            'service' => 'required|in:' . implode(',', self::SERVICES),
            'price_range' => 'nullable|string|max:255',
        ]);

        $exists = GarageService::where('garage_id', $data['garage_id'])
            ->where('service', $data['service'])
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Ce service existe déjà pour ce garage']);
        }

        $entry = GarageService::create($data);

        return response()->json(['success' => true, 'message' => 'Service ajouté avec succès', 'data' => $entry]);
    }

    public function show(GarageService $garageService)
    {
        $garageService->load('garage');

        return response()->json(['success' => true, 'data' => $garageService]);
    }

    public function update(Request $request, GarageService $garageService)
    {
        $data = $request->validate([
            'garage_id' => 'required|exists:garages,id_garage',
            'service' => 'required|in:' . implode(',', self::SERVICES),
            'price_range' => 'nullable|string|max:255',
        ]);

        $exists = GarageService::where('garage_id', $data['garage_id'])
            ->where('service', $data['service'])
            ->where('id_gara_service', '!=', $garageService->id_gara_service)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Ce service existe déjà pour ce garage']);
        }

        $garageService->update($data);

        return response()->json(['success' => true, 'message' => 'Service modifié avec succès']);
    }

    public function destroy(GarageService $garageService)
    {
        $garageService->delete();

        return response()->json(['success' => true, 'message' => 'Service supprimé']);
    }
}
