<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemandesController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX
    // GET /admin/partner-requests?status=pending&type=&city=
    // ─────────────────────────────────────────────
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');
        $type   = $request->input('type', '');
        $city   = $request->input('city', '');

        // ── KPIs globaux (une seule requête) ──────
        $kpis = DB::table('partner_requests')
            ->selectRaw("
                SUM(status = 'pending')   as pending,
                SUM(status = 'contacted') as contacted,
                SUM(status = 'approved')  as approved,
                SUM(status = 'rejected')  as rejected
            ")
            ->first();

        // ── Requête principale ─────────────────────
        $query = DB::table('partner_requests')
            ->where('status', $status)
            ->orderBy('created_at', in_array($status, ['pending','contacted']) ? 'asc' : 'desc');

        if ($type) $query->where('type', $type);
        if ($city) $query->where('city', 'like', "%{$city}%");

        $requests = $query->paginate(20)->withQueryString();

        // Villes disponibles pour le filtre
        $cities = DB::table('partner_requests')
            ->distinct()->orderBy('city')->pluck('city');

        return view('pages.partner-requests', compact(
            'requests', 'kpis', 'cities',
            'status', 'type', 'city'
        ));
    }

    // ─────────────────────────────────────────────
    // SHOW — JSON (pour modal ou usage API)
    // GET /admin/partner-requests/{id}
    // ─────────────────────────────────────────────
    public function show(int $id): JsonResponse
    {
        $req = DB::table('partner_requests')->where('id_demande', $id)->first();
        if (!$req) return response()->json(['success' => false, 'message' => 'Demande introuvable.'], 404);

        $adminName = $req->admin_id
            ? DB::table('admins')->where('id', $req->admin_id)->value('name')
            : null;

        return response()->json([
            'success' => true,
            'data'    => array_merge((array) $req, ['admin_name' => $adminName]),
        ]);
    }

    // ─────────────────────────────────────────────
    // APPROVE — Créer compte pro + établissement
    // POST /admin/partner-requests/{id}/approve
    // Body: { plan, email, admin_notes }
    // ─────────────────────────────────────────────
    public function approve(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'plan'        => 'required|in:free,pro,premium',
            'email'       => 'required|email',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $req = DB::table('partner_requests')->where('id_demande', $id)->first();
        if (!$req) return response()->json(['success' => false, 'message' => 'Demande introuvable.'], 404);

        if (!in_array($req->status, ['pending','contacted'])) {
            return response()->json(['success' => false, 'message' => 'Cette demande a déjà été traitée.'], 409);
        }

        $ownerTable = $req->type === 'station' ? 'station_owners' : 'garage_owners';

        if (DB::table($ownerTable)->where('email', $validated['email'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Un compte pro existe déjà avec cet email.'], 409);
        }

        $tempPassword = Str::random(12);

        DB::beginTransaction();
        try {
            // 1. Créer le propriétaire
            $ownerId = DB::table($ownerTable)->insertGetId([
                'name'         => $req->contact_name,
                'email'        => $validated['email'],
                'password'     => Hash::make($tempPassword),
                'phone'        => $req->contact_phone,
                'company_name' => $req->business_name,
                'status'       => 'approved',
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            // 2. Créer l'établissement
            $entityTable = $req->type === 'station' ? 'stations' : 'garages';
            $linkTable   = $req->type === 'station' ? 'station_owner_station' : 'garage_owner_garage';
            $linkOwnerFk = $req->type === 'station' ? 'station_owner_id' : 'garage_owner_id';
            $linkEntFk   = $req->type === 'station' ? 'station_id'       : 'garage_id';

            $entityData = [
                'name'              => $req->business_name,
                'address'           => $req->address,
                'city'              => $req->city,
                'country'           => 'CI',
                'latitude'          => $req->latitude  ?? 5.3600,
                'longitude'         => $req->longitude ?? -4.0083,
                'phone'             => $req->contact_phone,
                'subscription_type' => $validated['plan'],
                'is_active'         => true,
                'is_verified'       => false,
                'views_count'       => 0,
                'created_at'        => now(),
                'updated_at'        => now(),
            ];

            if ($req->type === 'garage') {
                $entityData['type']         = 'garage_general';
                $entityData['rating']       = 0;
                $entityData['rating_count'] = 0;
            }

            $entityId = DB::table($entityTable)->insertGetId($entityData);

            // 3. Lier owner ↔ entité
            DB::table($linkTable)->insert([
                $linkOwnerFk => $ownerId,
                $linkEntFk   => $entityId,
                'role'       => 'owner',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Mettre à jour la demande
            DB::table('partner_requests')->where('id_demande', $id)->update([
                'status'       => 'approved',
                'admin_notes'  => $validated['admin_notes'] ?? null,
                'admin_id'     => auth()->id(),
                'processed_at' => now(),
                'updated_at'   => now(),
            ]);

            DB::commit();

            // TODO: Mail::to($validated['email'])->send(new PartnerWelcomeMail($req, $tempPassword));

            return response()->json([
                'success' => true,
                'message' => "Demande approuvée. Compte pro créé pour « {$req->business_name} ».",
                'data'    => ['owner_id' => $ownerId, 'entity_id' => $entityId],
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────
    // CONTACT — Marquer comme contacté
    // POST /admin/partner-requests/{id}/contact
    // ─────────────────────────────────────────────
    public function contact(int $id): JsonResponse
    {
        $req = DB::table('partner_requests')->where('id_demande', $id)->first();
        if (!$req) return response()->json(['success' => false, 'message' => 'Demande introuvable.'], 404);

        if ($req->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Seules les demandes en attente peuvent être marquées contactées.'], 409);
        }

        DB::table('partner_requests')->where('id_demande', $id)->update([
            'status'     => 'contacted',
            'admin_id'   => auth()->id(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Demande marquée comme contactée.']);
    }

    // ─────────────────────────────────────────────
    // REJECT
    // POST /admin/partner-requests/{id}/reject
    // Body: { reason, message }
    // ─────────────────────────────────────────────
    public function reject(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'reason'  => 'required|string|max:200',
            'message' => 'nullable|string|max:500',
        ]);

        $req = DB::table('partner_requests')->where('id_demande', $id)->first();
        if (!$req) return response()->json(['success' => false, 'message' => 'Demande introuvable.'], 404);

        if ($req->status === 'approved') {
            return response()->json(['success' => false, 'message' => 'Impossible de rejeter une demande déjà approuvée.'], 409);
        }

        $notes = $validated['reason'];
        if (!empty($validated['message'])) {
            $notes .= "\n\nMessage : " . $validated['message'];
        }

        DB::table('partner_requests')->where('id_demande', $id)->update([
            'status'       => 'rejected',
            'admin_notes'  => $notes,
            'admin_id'     => auth()->id(),
            'processed_at' => now(),
            'updated_at'   => now(),
        ]);

        // TODO: envoyer email de rejet si message renseigné

        return response()->json(['success' => true, 'message' => 'Demande rejetée.']);
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // DELETE /admin/partner-requests/{id}
    // ─────────────────────────────────────────────
    public function destroy(int $id): JsonResponse
    {
        $exists = DB::table('partner_requests')->where('id_demande', $id)->exists();
        if (!$exists) return response()->json(['success' => false, 'message' => 'Demande introuvable.'], 404);

        DB::table('partner_requests')->where('id_demande', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Demande supprimée.']);
    }
}
