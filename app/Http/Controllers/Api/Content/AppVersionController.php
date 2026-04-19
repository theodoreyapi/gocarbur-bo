<?php

namespace App\Http\Controllers\Api\Content;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AppVersionController extends Controller
{
    // ─────────────────────────────────────────────
    // CHECK — Vérifier si une mise à jour est disponible
    // GET /app/version?platform=android&current_version=1.0.0
    // ─────────────────────────────────────────────
    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'platform'        => 'required|in:android,ios',
            'current_version' => 'required|string|max:20',
        ]);

        $platform       = $request->input('platform');
        $currentVersion = $request->input('current_version');

        $latest = DB::table('app_versions')
            ->where('platform', $platform)
            ->where('is_current', true)
            ->first();

        if (!$latest) {
            return response()->json([
                'success'          => true,
                'update_available' => false,
                'message'          => 'Aucune version trouvée pour cette plateforme.',
            ]);
        }

        $updateAvailable = version_compare($currentVersion, $latest->version, '<');

        return response()->json([
            'success' => true,
            'data'    => [
                'current_version'  => $currentVersion,
                'latest_version'   => $latest->version,
                'build_number'     => $latest->build_number,
                'update_available' => $updateAvailable,
                'force_update'     => $updateAvailable ? $latest->force_update : 'none',
                'store_url'        => $latest->store_url,
                'changelog'        => $latest->changelog,
                'released_at'      => $latest->released_at,
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // PUBLIC SETTINGS — Configuration publique de l'app
    // GET /app/settings
    // ─────────────────────────────────────────────
    public function publicSettings(): JsonResponse
    {
        $rows = DB::table('app_settings')
            ->where('is_public', true)
            ->get(['key', 'value', 'type']);

        $settings = $rows->mapWithKeys(fn ($row) => [
            $row->key => $this->castValue($row->value, $row->type)
        ]);

        return response()->json(['success' => true, 'data' => $settings]);
    }

    // ─────────────────────────────────────────────
    // HELPER — Caster les valeurs selon leur type
    // ─────────────────────────────────────────────
    private function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'integer' => (int)   $value,
            'boolean' => (bool)  $value,
            'decimal' => (float) $value,
            'json'    => json_decode($value, true),
            default   => $value,
        };
    }
}
