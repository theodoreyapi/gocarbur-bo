<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ParametresController extends Controller
{
    /**
     * Display settings page.
     */
    public function index()
    {
        // Charger tous les paramètres groupés
        $groups = AppSetting::all()->groupBy('group');

        // Helpers : accès direct par clé dans la vue
        $settings = AppSetting::all()->keyBy('key');

        return view('pages.settings', compact('groups', 'settings'));
    }

    /**
     * Save settings for a given group.
     * POST /settings/{group}
     */
    public function saveGroup(Request $request, string $group)
    {
        // Récupérer les clés connues pour ce groupe
        $keys = AppSetting::where('group', $group)->pluck('key');

        foreach ($keys as $key) {
            if (!$request->has($key)) {
                // Champ non présent = checkbox décochée → false
                $setting = AppSetting::where('key', $key)->first();
                if ($setting && $setting->type === 'boolean') {
                    AppSetting::set($key, '0');
                }
                continue;
            }

            $value = $request->input($key);

            // Masquer les champs de type password non modifiés
            if ($value === '••••••••••••••••' || $value === '') {
                $setting = AppSetting::where('key', $key)->first();
                if ($setting && in_array($setting->type, ['string']) && str_contains($key, '_key') || str_contains($key, '_secret') || str_contains($key, '_token')) {
                    continue; // ne pas écraser les secrets si non modifiés
                }
            }

            AppSetting::set($key, $value);
        }

        // Invalider tout le cache settings
        Cache::flush(); // ou Cache::tags('settings')->flush() si tu utilises des tags

        return redirect()->route('settings.index')
            ->with('toast_success', 'Paramètres "' . $group . '" sauvegardés avec succès.');
    }

    /* ── Resource stubs ──────────────────────────── */
    public function create() {}
    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}
