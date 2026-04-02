<?php

namespace App\Console\Commands;

use App\Models\OtpCode;
use App\Models\Station;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

// ═══════════════════════════════════════════════════════════════
// SendFuelAlerts
// Planification: hourly → php artisan fuel:send-alerts
// ═══════════════════════════════════════════════════════════════

class SendFuelAlerts extends Command
{
    protected $signature   = 'fuel:send-alerts';
    protected $description = 'Envoie des alertes de prix carburant aux utilisateurs premium';

    public function handle(FirebaseService $firebase): void
    {
        $this->info('Envoi des alertes carburant...');

        // Trouver les stations qui ont baissé leurs prix dans la dernière heure
        $cheaperStations = DB::table('fuel_price_history as h')
            ->join('stations as s', 's.id', '=', 'h.station_id')
            ->where('h.changed_at', '>=', now()->subHour())
            ->where('h.new_price', '<', DB::raw('h.old_price'))
            ->select('h.station_id', 'h.fuel_type', 'h.new_price', 'h.old_price', 's.name', 's.city')
            ->get();

        if ($cheaperStations->isEmpty()) {
            $this->info('Aucune baisse de prix détectée.');
            return;
        }

        $sent = 0;

        foreach ($cheaperStations as $entry) {
            // Utilisateurs premium de la même ville
            $tokens = User::where('is_active', true)
                ->where('subscription_type', 'premium')
                ->where('subscription_expires_at', '>=', now())
                ->whereNotNull('fcm_token')
                ->where('city', 'like', "%{$entry->city}%")
                ->pluck('fcm_token')
                ->toArray();

            if (empty($tokens)) continue;

            $diff  = $entry->old_price - $entry->new_price;
            $title = 'Prix carburant en baisse !';
            $body  = "{$entry->name} ({$entry->city}) : {$entry->fuel_type} à {$entry->new_price} FCFA/L (-{$diff} FCFA)";

            $result = $firebase->sendMulticast($tokens, [
                'title' => $title,
                'body'  => $body,
            ], [
                'type'       => 'fuel_alert',
                'station_id' => (string) $entry->station_id,
                'fuel_type'  => $entry->fuel_type,
                'price'      => (string) $entry->new_price,
            ]);

            $sent += $result['success_count'];
            $this->line("Station {$entry->name}: {$result['success_count']} notifications envoyées.");
        }

        $this->info("Total: {$sent} alertes carburant envoyées.");
    }
}

// ═══════════════════════════════════════════════════════════════
// CleanExpiredOtp
// Planification: every 30 minutes → php artisan otp:clean
// ═══════════════════════════════════════════════════════════════

class CleanExpiredOtp extends Command
{
    protected $signature   = 'otp:clean';
    protected $description = 'Supprime les codes OTP expirés ou utilisés';

    public function handle(): void
    {
        $deleted = OtpCode::where(fn($q) =>
            $q->where('expires_at', '<', now())
              ->orWhere('is_used', true)
        )->delete();

        $this->info("{$deleted} codes OTP supprimés.");
    }
}
