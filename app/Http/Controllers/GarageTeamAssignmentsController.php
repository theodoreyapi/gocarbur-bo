<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Garage;
use App\Models\GarageOwner;
use App\Models\GarageOwnerGarage;
use Illuminate\Http\Request;

class GarageTeamAssignmentsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $role = $request->get('role', '');
        $garageId = $request->get('garage_id', '');

        $query = GarageOwnerGarage::query()->with(['owner', 'garage']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('owner', fn ($o) => $o->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('garage', fn ($g) => $g->where('name', 'like', "%{$search}%"));
            });
        }
        if ($role) {
            $query->where('role', $role);
        }
        if ($garageId) {
            $query->where('garage_id', $garageId);
        }

        $assignments = $query->orderByDesc('id_gara_owner_gara')->paginate(15)->withQueryString();
        $total = GarageOwnerGarage::count();

        $kpis = [
            'total' => $total,
            'owners' => GarageOwnerGarage::where('role', 'owner')->count(),
            'managers' => GarageOwnerGarage::where('role', 'manager')->count(),
            'employees' => GarageOwnerGarage::where('role', 'employee')->count(),
        ];

        $allOwners = GarageOwner::orderBy('name')->get(['id_gara_owner', 'name', 'email']);
        $allGarages = Garage::orderBy('name')->get(['id_garage', 'name', 'city']);

        return view('pages.garage-team-assignments', compact(
            'assignments', 'kpis', 'total', 'allOwners', 'allGarages',
            'search', 'role', 'garageId'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'owner_id' => 'required|exists:garage_owners,id_gara_owner',
            'garage_id' => 'required|exists:garages,id_garage',
            'role' => 'required|in:owner,manager,employee',
        ]);

        $exists = GarageOwnerGarage::where('owner_id', $data['owner_id'])
            ->where('garage_id', $data['garage_id'])
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Ce propriétaire est déjà rattaché à ce garage']);
        }

        $assignment = GarageOwnerGarage::create($data);

        return response()->json(['success' => true, 'message' => 'Affectation ajoutée avec succès', 'data' => $assignment]);
    }

    public function show(GarageOwnerGarage $assignment)
    {
        $assignment->load(['owner', 'garage']);

        return response()->json(['success' => true, 'data' => $assignment]);
    }

    public function update(Request $request, GarageOwnerGarage $assignment)
    {
        $data = $request->validate([
            'owner_id' => 'required|exists:garage_owners,id_gara_owner',
            'garage_id' => 'required|exists:garages,id_garage',
            'role' => 'required|in:owner,manager,employee',
        ]);

        $exists = GarageOwnerGarage::where('owner_id', $data['owner_id'])
            ->where('garage_id', $data['garage_id'])
            ->where('id_gara_owner_gara', '!=', $assignment->id_gara_owner_gara)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Ce propriétaire est déjà rattaché à ce garage']);
        }

        $assignment->update($data);

        return response()->json(['success' => true, 'message' => 'Affectation modifiée avec succès']);
    }

    public function destroy(GarageOwnerGarage $assignment)
    {
        $assignment->delete();

        return response()->json(['success' => true, 'message' => 'Affectation supprimée']);
    }
}
