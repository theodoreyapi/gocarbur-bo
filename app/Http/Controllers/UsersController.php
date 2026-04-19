<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX — Liste paginée avec filtres
    // GET /admin/users?search=&plan=&status=&city=&page=
    // ─────────────────────────────────────────────
    public function index(Request $request)
    {
        $search = $request->input('search');
        $plan   = $request->input('plan');
        $status = $request->input('status');
        $city   = $request->input('city');
        $limit  = 25;

        // ── KPIs ──────────────────────────────────
        $kpis = [
            'total'     => DB::table('users_carbur')->whereNull('deleted_at')->count(),
            'premium'   => DB::table('users_carbur')->whereNull('deleted_at')->where('subscription_type', 'premium')->count(),
            'active'    => DB::table('users_carbur')->whereNull('deleted_at')->where('is_active', true)->count(),
            'suspended' => DB::table('users_carbur')->whereNull('deleted_at')->where('is_active', false)->count(),
        ];

        // ── Requête principale ─────────────────────
        $query = DB::table('users_carbur')->whereNull('deleted_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($plan)   $query->where('subscription_type', $plan);
        if ($status === 'active')    $query->where('is_active', true);
        if ($status === 'suspended') $query->where('is_active', false);
        if ($city)   $query->where('city', $city);

        $total   = $query->count();
        $users   = $query->orderByDesc('created_at')
            ->paginate($limit)
            ->withQueryString();

        // Nombre de véhicules par user (en une seule requête)
        $userIds       = $users->pluck('id_user_carbu');
        $vehicleCounts = DB::table('vehicles')
            ->whereIn('user_id', $userIds)
            ->whereNull('deleted_at')
            ->select('user_id', DB::raw('COUNT(*) as count'))
            ->groupBy('user_id')
            ->pluck('count', 'user_id');

        // Villes distinctes pour le filtre
        $cities = DB::table('users_carbur')
            ->whereNull('deleted_at')
            ->whereNotNull('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return view('pages.users', compact(
            'users', 'kpis', 'vehicleCounts', 'cities',
            'search', 'plan', 'status', 'city', 'total'
        ));
    }

    // ─────────────────────────────────────────────
    // SHOW — Détail d'un utilisateur (JSON pour modal)
    // GET /admin/users/{id}
    // ─────────────────────────────────────────────
    public function show(int $id): JsonResponse
    {
        $user = DB::table('users_carbur')
            ->where('id_user_carbu', $id)
            ->whereNull('deleted_at')
            ->first([
                'id_user_carbu', 'name', 'email', 'phone', 'city',
                'avatar_url', 'subscription_type', 'subscription_expires_at',
                'is_active', 'last_login_at', 'created_at',
            ]);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Utilisateur introuvable.'], 404);
        }

        $vehicleCount  = DB::table('vehicles')->where('user_id', $id)->whereNull('deleted_at')->count();
        $documentCount = DB::table('documents')
            ->join('vehicles', 'vehicles.id_vehicule', '=', 'documents.vehicle_id')
            ->where('vehicles.user_id', $id)
            ->whereNull('documents.deleted_at')
            ->whereNull('vehicles.deleted_at')
            ->count();
        $reminderCount = DB::table('reminders')->where('user_id', $id)->where('is_dismissed', false)->count();
        $reviewCount   = DB::table('reviews')->where('user_id', $id)->whereNull('deleted_at')->count();

        $activeSub = DB::table('subscriptions')
            ->where('subscribable_type', 'App\Models\UserCarbur')
            ->where('subscribable_id', $id)
            ->where('status', 'active')
            ->orderByDesc('expires_at')
            ->first(['plan', 'expires_at', 'billing_cycle', 'payment_method']);

        return response()->json([
            'success' => true,
            'data'    => array_merge((array) $user, [
                'vehicle_count'  => $vehicleCount,
                'document_count' => $documentCount,
                'reminder_count' => $reminderCount,
                'review_count'   => $reviewCount,
                'subscription'   => $activeSub,
            ]),
        ]);
    }

    // ─────────────────────────────────────────────
    // STORE — Créer un utilisateur
    // POST /admin/users
    // ─────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'phone'             => 'required|string|max:20|unique:users_carbur,phone',
            'email'             => 'nullable|email|unique:users_carbur,email',
            'city'              => 'nullable|string|max:100',
            'subscription_type' => 'required|in:free,premium',
            'password'          => 'required|string|min:6',
        ]);

        $expiresAt = $validated['subscription_type'] === 'premium'
            ? now()->addMonth()->toDateTimeString()
            : null;

        $id = DB::table('users_carbur')->insertGetId([
            'name'                    => $validated['name'],
            'phone'                   => $validated['phone'],
            'email'                   => $validated['email'] ?? null,
            'city'                    => $validated['city'] ?? null,
            'password'                => Hash::make($validated['password']),
            'subscription_type'       => $validated['subscription_type'],
            'subscription_expires_at' => $expiresAt,
            'is_active'               => true,
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        $user = DB::table('users_carbur')->where('id_user_carbu', $id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur créé avec succès.',
            'data'    => $user,
        ], 201);
    }

    // ─────────────────────────────────────────────
    // GRANT PREMIUM — Accorder le premium manuellement
    // POST /admin/users/{id}/grant-premium
    // ─────────────────────────────────────────────
    public function grantPremium(Request $request, int $id): JsonResponse
    {
        $request->validate(['months' => 'sometimes|integer|min:1|max:24']);

        $months = $request->input('months', 1);

        $user = DB::table('users_carbur')->where('id_user_carbu', $id)->whereNull('deleted_at')->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Utilisateur introuvable.'], 404);
        }

        // Calculer la nouvelle date d'expiration (prolonger si déjà premium)
        $currentExpiry = $user->subscription_expires_at && $user->subscription_expires_at > now()->toDateTimeString()
            ? \Carbon\Carbon::parse($user->subscription_expires_at)
            : now();

        $newExpiry = $currentExpiry->addMonths($months);

        DB::table('users_carbur')->where('id_user_carbu', $id)->update([
            'subscription_type'       => 'premium',
            'subscription_expires_at' => $newExpiry->toDateTimeString(),
            'updated_at'              => now(),
        ]);

        // Créer un enregistrement de subscription
        DB::table('subscriptions')->insert([
            'subscribable_type' => 'App\Models\UserCarbur',
            'subscribable_id'   => $id,
            'plan'              => 'user_premium',
            'amount'            => 0,
            'billing_cycle'     => 'mensuel',
            'starts_at'         => now()->toDateString(),
            'expires_at'        => $newExpiry->toDateString(),
            'status'            => 'active',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return response()->json([
            'success'    => true,
            'message'    => "Premium accordé jusqu'au " . $newExpiry->locale('fr')->isoFormat('D MMM YYYY') . '.',
            'expires_at' => $newExpiry->toDateTimeString(),
        ]);
    }

    // ─────────────────────────────────────────────
    // SUSPEND — Suspendre un utilisateur
    // POST /admin/users/{id}/suspend
    // ─────────────────────────────────────────────
    public function suspend(int $id): JsonResponse
    {
        $exists = DB::table('users_carbur')->where('id_user_carbu', $id)->whereNull('deleted_at')->exists();
        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Utilisateur introuvable.'], 404);
        }

        DB::table('users_carbur')->where('id_user_carbu', $id)->update([
            'is_active'  => false,
            'updated_at' => now(),
        ]);

        // Révoquer tous les tokens
        DB::table('personal_access_tokens')
            ->where('tokenable_type', 'App\Models\UserCarbur')
            ->where('tokenable_id', $id)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Utilisateur suspendu.']);
    }

    // ─────────────────────────────────────────────
    // REACTIVATE — Réactiver un utilisateur
    // POST /admin/users/{id}/reactivate
    // ─────────────────────────────────────────────
    public function reactivate(int $id): JsonResponse
    {
        $exists = DB::table('users_carbur')->where('id_user_carbu', $id)->whereNull('deleted_at')->exists();
        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Utilisateur introuvable.'], 404);
        }

        DB::table('users_carbur')->where('id_user_carbu', $id)->update([
            'is_active'  => true,
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Utilisateur réactivé.']);
    }

    // ─────────────────────────────────────────────
    // DESTROY — Supprimer (soft delete)
    // DELETE /admin/users/{id}
    // ─────────────────────────────────────────────
    public function destroy(int $id): JsonResponse
    {
        $exists = DB::table('users_carbur')->where('id_user_carbu', $id)->whereNull('deleted_at')->exists();
        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Utilisateur introuvable.'], 404);
        }

        DB::table('users_carbur')->where('id_user_carbu', $id)->update([
            'deleted_at' => now(),
            'is_active'  => false,
        ]);

        DB::table('personal_access_tokens')
            ->where('tokenable_type', 'App\Models\UserCarbur')
            ->where('tokenable_id', $id)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Utilisateur supprimé.']);
    }

    // ─────────────────────────────────────────────
    // EXPORT — Export CSV
    // GET /admin/users/export
    // ─────────────────────────────────────────────
    public function export(Request $request)
    {
        $plan   = $request->input('plan');
        $status = $request->input('status');
        $city   = $request->input('city');

        $query = DB::table('users_carbur')->whereNull('deleted_at')
            ->select('id_user_carbu', 'name', 'email', 'phone', 'city', 'subscription_type', 'is_active', 'last_login_at', 'created_at');

        if ($plan)   $query->where('subscription_type', $plan);
        if ($status === 'active')    $query->where('is_active', true);
        if ($status === 'suspended') $query->where('is_active', false);
        if ($city)   $query->where('city', $city);

        $users = $query->orderByDesc('created_at')->get();

        $headers = ['ID', 'Nom', 'Email', 'Téléphone', 'Ville', 'Plan', 'Statut', 'Dernière connexion', 'Inscrit le'];
        $rows    = $users->map(fn ($u) => [
            $u->id_user_carbu,
            $u->name,
            $u->email ?? '',
            $u->phone,
            $u->city ?? '',
            $u->subscription_type,
            $u->is_active ? 'Actif' : 'Suspendu',
            $u->last_login_at ?? '',
            $u->created_at,
        ]);

        $csv = collect([$headers])->merge($rows)->map(
            fn ($row) => implode(';', array_map(fn ($v) => '"' . str_replace('"', '""', $v) . '"', $row))
        )->implode("\n");

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="utilisateurs_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }
}
