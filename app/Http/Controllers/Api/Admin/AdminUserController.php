<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /** GET /admin/users */
    public function index(Request $request): JsonResponse
    {
        $users = User::withTrashed()
            ->when($request->search, fn($q) =>
                $q->where('name','like',"%{$request->search}%")
                  ->orWhere('phone','like',"%{$request->search}%")
            )
            ->when($request->subscription, fn($q) => $q->where('subscription_type', $request->subscription))
            ->when($request->city,         fn($q) => $q->where('city', $request->city))
            ->when($request->active !== null, fn($q) => $q->where('is_active', $request->boolean('active')))
            ->withCount(['vehicles','reminders'])
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 25));

        return response()->json(['success' => true, 'data' => $users]);
    }

    /** GET /admin/users/{id} */
    public function show(int $id): JsonResponse
    {
        $user = User::withTrashed()
            ->with(['vehicles.documents','subscriptions','reminders'])
            ->withCount(['vehicles','reminders','favorites','reviews'])
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $user]);
    }

    /** PUT /admin/users/{id} */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name' => 'sometimes|string|max:100',
            'city' => 'sometimes|string|max:100',
        ]);
        $user->update($data);

        return response()->json(['success' => true, 'message' => 'Utilisateur mis à jour.', 'data' => $user->fresh()]);
    }

    /** PATCH /admin/users/{id}/toggle-active */
    public function toggleActive(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        $action = $user->is_active ? 'réactivé' : 'suspendu';
        return response()->json(['success' => true, 'message' => "Utilisateur {$action}.", 'is_active' => $user->is_active]);
    }

    /** POST /admin/users/{id}/grant-premium */
    public function grantPremium(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $data = $request->validate(['months' => 'required|integer|min:1|max:24']);

        $expires = now()->addMonths($data['months']);
        $user->update([
            'subscription_type'       => 'premium',
            'subscription_expires_at' => $expires,
        ]);

        // Créer un enregistrement de souscription gratuite (offerte)
        $user->subscriptions()->create([
            'plan'       => 'user_premium',
            'amount'     => 0,
            'starts_at'  => now(),
            'expires_at' => $expires,
            'status'     => 'active',
            'payment_method' => 'especes',
        ]);

        return response()->json([
            'success'    => true,
            'message'    => "Premium accordé pour {$data['months']} mois.",
            'expires_at' => $expires,
        ]);
    }

    /** DELETE /admin/users/{id} */
    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->tokens()->delete();
        $user->delete();

        return response()->json(['success' => true, 'message' => 'Compte supprimé.']);
    }

    /** GET /admin/users/export/csv */
    public function exportCsv(Request $request)
    {
        $users = User::when($request->subscription, fn($q) => $q->where('subscription_type', $request->subscription))
            ->select(['id','name','phone','city','subscription_type','created_at'])
            ->get();

        $csv  = "ID,Nom,Téléphone,Ville,Abonnement,Inscription\n";
        foreach ($users as $u) {
            $csv .= "{$u->id},{$u->name},{$u->phone},{$u->city},{$u->subscription_type},{$u->created_at}\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
