<?php

namespace App\Http\Controllers;

use App\Models\AppVersion;
use App\Models\UserCarbur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VersionsController extends Controller
{
    /**
     * Display versions dashboard.
     */
    public function index(Request $request)
    {
        /* ── Versions actives par plateforme ────────── */
        $currentAndroid = AppVersion::current()->forPlatform('android')->latest('released_at')->first();
        $currentIos     = AppVersion::current()->forPlatform('ios')->latest('released_at')->first();

        /* ── Config mise à jour par plateforme ──────── */
        // On utilise une app_config ou le minimum depuis l'historique
        // Ici on calcule la version minimum (la plus ancienne avec force_update = required)
        $minAndroid = AppVersion::forPlatform('android')
            ->where('force_update', 'required')
            ->orderBy('released_at')
            ->value('version') ?? '1.0.0';

        $minIos = AppVersion::forPlatform('ios')
            ->where('force_update', 'required')
            ->orderBy('released_at')
            ->value('version') ?? '1.0.0';

        /* ── Historique (filtré) ─────────────────────── */
        $historyQuery = AppVersion::orderByDesc('released_at');

        if ($request->filled('platform') && $request->platform !== 'all') {
            $historyQuery->forPlatform($request->platform);
        }

        $history = $historyQuery->get();

        /* ── Stats adoption (simulation basée sur usersCount) ── */
        // En vrai, tu stockerais la version de l'app dans users_carbur via FCM/login
        $totalUsers = UserCarbur::where('is_active', true)->count() ?: 1;

        return view('pages.app-versions', compact(
            'currentAndroid',
            'currentIos',
            'minAndroid',
            'minIos',
            'history',
            'totalUsers'
        ));
    }

    /**
     * Store a new version declaration.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'version'      => ['required', 'string', 'max:20', 'regex:/^\d+\.\d+\.\d+$/'],
            'platform'     => 'required|in:android,ios,both',
            'build_number' => 'required|integer|min:1',
            'force_update' => 'required|in:none,optional,required',
            'store_url'    => 'nullable|url|max:500',
            'changelog'    => 'nullable|string|max:2000',
            'released_at'  => 'nullable|date',
            'is_current'   => 'nullable|boolean',
            'min_version'  => 'nullable|string|max:20|regex:/^\d+\.\d+\.\d+$/',
        ]);

        $platforms = $validated['platform'] === 'both'
            ? ['android', 'ios']
            : [$validated['platform']];

        foreach ($platforms as $platform) {
            // Si marquée comme actuelle, dépasser les autres
            if (!empty($validated['is_current'])) {
                AppVersion::forPlatform($platform)->update(['is_current' => false]);
            }

            AppVersion::create([
                'platform'     => $platform,
                'version'      => $validated['version'],
                'build_number' => $validated['build_number'],
                'force_update' => $validated['force_update'],
                'store_url'    => $validated['store_url'] ?? null,
                'changelog'    => $validated['changelog'] ?? null,
                'is_current'   => !empty($validated['is_current']),
                'released_at'  => $validated['released_at'] ?? now()->toDateString(),
            ]);

            // Si version minimum définie et force update required, marquer les anciennes
            if (!empty($validated['min_version']) && $validated['force_update'] === 'required') {
                AppVersion::forPlatform($platform)
                    ->where('version', '<', $validated['min_version'])
                    ->where('force_update', 'none')
                    ->update(['force_update' => 'required']);
            }
        }

        return redirect()->route('app-versions.index')
            ->with('toast_success', 'Version ' . $validated['version'] . ' déclarée avec succès.');
    }

    /**
     * Return one version as JSON (modal detail).
     */
    public function show(string $id)
    {
        $v = AppVersion::findOrFail($id);
        return response()->json([
            'version'       => $v->version,
            'platform'      => $v->platform,
            'build_number'  => $v->build_number,
            'force_update'  => $v->force_update,
            'force_label'   => $v->force_update_label,
            'status_badge'  => $v->status_badge,
            'status_label'  => $v->status_label,
            'release_type'  => $v->release_type,
            'release_badge' => $v->release_type_badge,
            'store_url'     => $v->store_url ?? '—',
            'changelog'     => $v->changelog ?? '—',
            'released_at'   => $v->released_at?->format('d M Y') ?? '—',
        ]);
    }

    /**
     * Save platform config (store_url, force_update toggle, update message).
     * Called via PATCH /app-versions/{platform}/config
     */
    public function saveConfig(Request $request, string $platform)
    {
        $request->validate([
            'platform'     => 'in:android,ios',
            'force_update' => 'required|in:none,optional,required',
            'store_url'    => 'nullable|url',
        ]);

        // Applique la config à la version actuelle de cette plateforme
        AppVersion::current()->forPlatform($platform)->update([
            'force_update' => $request->force_update,
            'store_url'    => $request->store_url,
        ]);

        return redirect()->route('app-versions.index')
            ->with('toast_success', 'Configuration ' . strtoupper($platform) . ' sauvegardée.');
    }

    /* ── Resource stubs ──────────────────────────── */
    public function create() {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}
