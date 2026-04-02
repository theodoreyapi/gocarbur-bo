<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// ═══════════════════════════════════════════════════════════════
// CheckSubscription — vérifie que l'utilisateur est premium
// Usage: ->middleware('subscription:premium')
// ═══════════════════════════════════════════════════════════════

class CheckSubscription
{
    public function handle(Request $request, Closure $next, string $plan = 'premium'): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success'          => false,
                'message'          => 'Non authentifié.',
                'upgrade_required' => false,
            ], 401);
        }

        if (!$user->isPremium()) {
            return response()->json([
                'success'          => false,
                'message'          => 'Cette fonctionnalité est réservée aux abonnés Premium.',
                'upgrade_required' => true,
                'upgrade_url'      => '/subscription/plans',
            ], 403);
        }

        return $next($request);
    }
}

// ═══════════════════════════════════════════════════════════════
// CheckProPlan — vérifie le plan de l'espace pro
// Usage: ->middleware('pro.plan:pro,premium')
// ═══════════════════════════════════════════════════════════════

class CheckProPlan
{
    public function handle(Request $request, Closure $next, string ...$allowedPlans): Response
    {
        $owner = $request->user();

        if (!$owner) {
            return response()->json(['success' => false, 'message' => 'Non authentifié.'], 401);
        }

        // Récupérer les entités (stations ou garages) pour vérifier le plan
        $hasValidPlan = false;

        // Vérification via la station en cours (extraite de la route)
        $stationId = $request->route('stationId') ?? $request->route('id');
        if ($stationId && method_exists($owner, 'stations')) {
            $station = $owner->stations()->find($stationId);
            if ($station && in_array($station->subscription_type, $allowedPlans)) {
                $hasValidPlan = true;
            }
        }

        // Vérification via le garage en cours
        $garageId = $request->route('garageId') ?? $request->route('id');
        if (!$hasValidPlan && $garageId && method_exists($owner, 'garages')) {
            $garage = $owner->garages()->find($garageId);
            if ($garage && in_array($garage->subscription_type, $allowedPlans)) {
                $hasValidPlan = true;
            }
        }

        if (!$hasValidPlan) {
            $plansLabel = implode(' ou ', array_map(fn($p) => ucfirst($p), $allowedPlans));
            return response()->json([
                'success'          => false,
                'message'          => "Cette fonctionnalité nécessite un plan {$plansLabel}.",
                'upgrade_required' => true,
                'upgrade_url'      => '/pro/subscription/plans',
            ], 403);
        }

        return $next($request);
    }
}

// ═══════════════════════════════════════════════════════════════
// CheckProSubscription — vérifie que le compte pro a un abonnement actif
// Usage: ->middleware('pro.subscription')
// ═══════════════════════════════════════════════════════════════

class CheckProSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $owner = $request->user();

        if (!$owner) {
            return response()->json(['success' => false, 'message' => 'Non authentifié.'], 401);
        }

        // Les comptes en plan free peuvent accéder (accès limité)
        // La restriction granulaire est gérée par CheckProPlan

        if (!$owner->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Votre compte professionnel a été suspendu. Contactez le support.',
            ], 403);
        }

        return $next($request);
    }
}
