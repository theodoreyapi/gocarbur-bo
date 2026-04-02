<?php

namespace App\Services;

use App\Models\Station;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoService
{
    /**
     * Calculer la distance Haversine entre deux points (en km)
     */
    public function haversineDistance(
        float $lat1, float $lng1,
        float $lat2, float $lng2
    ): float {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Trouver les stations les moins chères dans un rayon donné
     */
    public function cheapestStationsNearby(
        float $lat,
        float $lng,
        string $fuelType = 'essence',
        int $radius = 10,
        int $limit = 10
    ): Collection {
        return Station::active()
            ->join('fuel_prices', 'stations.id', '=', 'fuel_prices.station_id')
            ->where('fuel_prices.fuel_type', $fuelType)
            ->where('fuel_prices.is_available', true)
            ->selectRaw("
                stations.*,
                fuel_prices.price,
                fuel_prices.updated_at_price,
                (6371 * ACOS(
                    COS(RADIANS(?)) * COS(RADIANS(stations.latitude)) *
                    COS(RADIANS(stations.longitude) - RADIANS(?)) +
                    SIN(RADIANS(?)) * SIN(RADIANS(stations.latitude))
                )) AS distance
            ", [$lat, $lng, $lat])
            ->having('distance', '<=', $radius)
            ->orderBy('fuel_prices.price')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtenir l'adresse à partir de coordonnées GPS (reverse geocoding)
     */
    public function reverseGeocode(float $lat, float $lng): ?array
    {
        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'latlng' => "{$lat},{$lng}",
                'key'    => config('services.google_maps.api_key'),
                'language'=> 'fr',
            ]);

            $data = $response->json();

            if ($data['status'] === 'OK' && !empty($data['results'])) {
                $result      = $data['results'][0];
                $components  = collect($result['address_components']);

                return [
                    'formatted_address' => $result['formatted_address'],
                    'city'  => $components->firstWhere(fn($c) => in_array('locality', $c['types']))['long_name'] ?? null,
                    'country'=> $components->firstWhere(fn($c) => in_array('country', $c['types']))['short_name'] ?? null,
                ];
            }
        } catch (\Throwable $e) {
            Log::error('GeoService reverseGeocode error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Envoyer des alertes carburant moins cher aux utilisateurs premium proches
     */
    public function sendFuelAlerts(Station $station, string $fuelType, float $newPrice): void
    {
        // Trouver les utilisateurs premium dans un rayon de 10 km
        // Nécessite que les users aient une latitude/longitude (position approximative par ville)
        $firebase = app(FirebaseService::class);

        $users = User::where('subscription_type', 'premium')
            ->where('is_active', true)
            ->whereNotNull('fcm_token')
            ->where('city', $station->city) // Approximation par ville
            ->pluck('fcm_token')
            ->toArray();

        if (empty($users)) return;

        $firebase->sendMulticast($users, [
            'title' => 'Prix carburant moins cher !',
            'body'  => "{$station->name} : {$fuelType} à {$newPrice} FCFA/L",
        ], [
            'type'       => 'fuel_alert',
            'station_id' => (string) $station->id,
            'fuel_type'  => $fuelType,
            'price'      => (string) $newPrice,
        ]);
    }

    /**
     * Vérifier si des coordonnées sont dans une bounding box (Côte d'Ivoire)
     */
    public function isInCoteDivoire(float $lat, float $lng): bool
    {
        // Bounding box approximative CI : lat [4.35, 10.73], lng [-8.60, -2.49]
        return $lat >= 4.35 && $lat <= 10.73
            && $lng >= -8.60 && $lng <= -2.49;
    }
}
