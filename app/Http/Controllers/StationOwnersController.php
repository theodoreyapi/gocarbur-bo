<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StationOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StationOwnersController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $status = $request->get('status', '');

        $query = StationOwner::query()->withCount(['stations', 'garages']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }
        if ($status) {
            $query->where('status', $status);
        }

        $owners = $query->orderByDesc('id_station_owner')->paginate(15)->withQueryString();
        $total = StationOwner::count();

        $kpis = [
            'total' => $total,
            'approved' => StationOwner::where('status', 'approved')->count(),
            'pending' => StationOwner::where('status', 'pending')->count(),
            'suspended' => StationOwner::where('status', 'suspended')->count(),
        ];

        return view('pages.station-owners', compact('owners', 'kpis', 'total', 'search', 'status'));
    }

    public function show(StationOwner $owner)
    {
        $owner->loadCount(['stations', 'garages']);
        $owner->load(['stations:id_station,name,city,owner_id', 'garages:id_garage,name,city,owner_id']);

        return response()->json(['success' => true, 'data' => $owner]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:station_owners,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'rccm' => 'nullable|string|max:255',
            'status' => 'in:pending,approved,suspended,rejected',
            'is_active' => 'boolean',
        ]);

        $data['password'] = Hash::make($data['password']);

        $owner = StationOwner::create($data);

        return response()->json(['success' => true, 'message' => 'Propriétaire ajouté avec succès', 'data' => $owner]);
    }

    public function update(Request $request, StationOwner $owner)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('station_owners', 'email')->ignore($owner->id_station_owner, 'id_station_owner')],
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'rccm' => 'nullable|string|max:255',
            'status' => 'in:pending,approved,suspended,rejected',
            'is_active' => 'boolean',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $owner->update($data);

        return response()->json(['success' => true, 'message' => 'Propriétaire modifié avec succès', 'data' => $owner]);
    }

    public function approve(StationOwner $owner)
    {
        $owner->update(['status' => 'approved', 'is_active' => true]);

        return response()->json(['success' => true, 'message' => 'Propriétaire approuvé', 'status' => $owner->status]);
    }

    public function suspend(StationOwner $owner)
    {
        $owner->status = $owner->status === 'suspended' ? 'approved' : 'suspended';
        $owner->is_active = $owner->status !== 'suspended';
        $owner->save();

        return response()->json([
            'success' => true,
            'message' => $owner->status === 'suspended' ? 'Compte suspendu' : 'Compte réactivé',
            'status' => $owner->status,
        ]);
    }

    public function destroy(StationOwner $owner)
    {
        $owner->delete();

        return response()->json(['success' => true, 'message' => 'Propriétaire supprimé']);
    }
}
