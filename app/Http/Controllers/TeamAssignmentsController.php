<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Station;
use App\Models\StationOwner;
use App\Models\StationOwnerStation;
use Illuminate\Http\Request;

class TeamAssignmentsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $role = $request->get('role', '');
        $stationId = $request->get('station_id', '');

        $query = StationOwnerStation::query()->with(['owner', 'station']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('owner', fn($o) => $o->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('station', fn($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }
        if ($role) {
            $query->where('role', $role);
        }
        if ($stationId) {
            $query->where('station_id', $stationId);
        }

        $assignments = $query->orderByDesc('id_stat_owner_stat')->paginate(15)->withQueryString();
        $total = StationOwnerStation::count();

        $kpis = [
            'total' => $total,
            'owners' => StationOwnerStation::where('role', 'owner')->count(),
            'managers' => StationOwnerStation::where('role', 'manager')->count(),
            'employees' => StationOwnerStation::where('role', 'employee')->count(),
        ];

        $allOwners = StationOwner::orderBy('name')->get(['id_station_owner', 'name', 'email']);
        $allStations = Station::orderBy('name')->get(['id_station', 'name', 'city']);

        return view('pages.team-assignments', compact(
            'assignments',
            'kpis',
            'total',
            'allOwners',
            'allStations',
            'search',
            'role',
            'stationId'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'owner_id' => 'required|exists:station_owners,id_station_owner',
            'station_id' => 'required|exists:stations,id_station',
            'role' => 'required|in:owner,manager,employee',
        ]);

        $exists = StationOwnerStation::where('owner_id', $data['owner_id'])
            ->where('station_id', $data['station_id'])
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Ce propriétaire est déjà rattaché à cette station']);
        }

        $assignment = StationOwnerStation::create($data);

        return response()->json(['success' => true, 'message' => 'Affectation ajoutée avec succès', 'data' => $assignment]);
    }

    public function show(StationOwnerStation $assignment)
    {
        $assignment->load(['owner', 'station']);

        return response()->json(['success' => true, 'data' => $assignment]);
    }

    public function update(Request $request, StationOwnerStation $assignment)
    {
        $data = $request->validate([
            'owner_id' => 'required|exists:station_owners,id_station_owner',
            'station_id' => 'required|exists:stations,id_station',
            'role' => 'required|in:owner,manager,employee',
        ]);

        $exists = StationOwnerStation::where('owner_id', $data['owner_id'])
            ->where('station_id', $data['station_id'])
            ->where('id_stat_owner_stat', '!=', $assignment->id_stat_owner_stat)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Ce propriétaire est déjà rattaché à cette station']);
        }

        $assignment->update($data);

        return response()->json(['success' => true, 'message' => 'Affectation modifiée avec succès']);
    }

    public function destroy(StationOwnerStation $assignment)
    {
        $assignment->delete();

        return response()->json(['success' => true, 'message' => 'Affectation supprimée']);
    }
}
