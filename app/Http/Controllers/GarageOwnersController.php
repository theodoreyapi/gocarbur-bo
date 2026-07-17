<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GarageOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class GarageOwnersController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $status = $request->get('status', '');

        $query = GarageOwner::query()->withCount('garages');

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

        $owners = $query->orderByDesc('id_gara_owner')->paginate(15)->withQueryString();
        $total = GarageOwner::count();

        $kpis = [
            'total' => $total,
            'approved' => GarageOwner::where('status', 'approved')->count(),
            'pending' => GarageOwner::where('status', 'pending')->count(),
            'suspended' => GarageOwner::where('status', 'suspended')->count(),
        ];

        return view('pages.garage-owners', compact('owners', 'kpis', 'total', 'search', 'status'));
    }

    public function show(GarageOwner $garageOwner)
    {
        $garageOwner->loadCount('garages');
        $garageOwner->load(['garages:id_garage,name,city,type,owner_id']);

        return response()->json(['success' => true, 'data' => $garageOwner]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:garage_owners,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'rccm' => 'nullable|string|max:255',
            'status' => 'in:pending,approved,suspended,rejected',
            'is_active' => 'boolean',
        ]);

        $data['password'] = Hash::make($data['password']);

        $owner = GarageOwner::create($data);

        return response()->json(['success' => true, 'message' => 'Propriétaire ajouté avec succès', 'data' => $owner]);
    }

    public function update(Request $request, GarageOwner $garageOwner)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('garage_owners', 'email')->ignore($garageOwner->id_gara_owner, 'id_gara_owner')],
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

        $garageOwner->update($data);

        return response()->json(['success' => true, 'message' => 'Propriétaire modifié avec succès', 'data' => $garageOwner]);
    }

    public function approve(GarageOwner $garageOwner)
    {
        $garageOwner->update(['status' => 'approved', 'is_active' => true]);

        return response()->json(['success' => true, 'message' => 'Propriétaire approuvé', 'status' => $garageOwner->status]);
    }

    public function suspend(GarageOwner $garageOwner)
    {
        $garageOwner->status = $garageOwner->status === 'suspended' ? 'approved' : 'suspended';
        $garageOwner->is_active = $garageOwner->status !== 'suspended';
        $garageOwner->save();

        return response()->json([
            'success' => true,
            'message' => $garageOwner->status === 'suspended' ? 'Compte suspendu' : 'Compte réactivé',
            'status' => $garageOwner->status,
        ]);
    }

    public function destroy(GarageOwner $garageOwner)
    {
        $garageOwner->delete();

        return response()->json(['success' => true, 'message' => 'Propriétaire supprimé']);
    }
}
