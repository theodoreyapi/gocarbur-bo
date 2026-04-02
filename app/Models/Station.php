<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Station extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'brand', 'address', 'city', 'country',
        'latitude', 'longitude', 'phone', 'whatsapp',
        'logo_url', 'photos', 'opens_at', 'closes_at',
        'is_open_24h', 'is_verified', 'subscription_type',
        'subscription_expires_at', 'views_count', 'is_active', 'description',
    ];

    protected $casts = [
        'photos'                  => 'array',
        'is_open_24h'             => 'boolean',
        'is_verified'             => 'boolean',
        'is_active'               => 'boolean',
        'subscription_expires_at' => 'datetime',
        'latitude'                => 'decimal:7',
        'longitude'               => 'decimal:7',
    ];

    // ── Relations ──────────────────────────────────────────────────

    public function fuelPrices()
    {
        return $this->hasMany(FuelPrice::class);
    }

    public function services()
    {
        return $this->hasMany(StationService::class);
    }

    public function promotions()
    {
        return $this->morphMany(Promotion::class, 'promotable');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable')
            ->where('is_approved', true);
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }

    public function views()
    {
        return $this->hasMany(StationView::class);
    }

    public function fuelLogs()
    {
        return $this->hasMany(FuelLog::class);
    }

    public function priceHistory()
    {
        return $this->hasMany(FuelPriceHistory::class);
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeNearby($query, float $lat, float $lng, int $radius = 5)
    {
        // Distance Haversine en km (MySQL natif)
        return $query->selectRaw("
                *,
                (6371 * ACOS(
                    COS(RADIANS(?)) * COS(RADIANS(latitude)) *
                    COS(RADIANS(longitude) - RADIANS(?)) +
                    SIN(RADIANS(?)) * SIN(RADIANS(latitude))
                )) AS distance
            ", [$lat, $lng, $lat])
            ->having('distance', '<=', $radius)
            ->orderBy('distance');
    }

    public function scopePro($query)
    {
        return $query->whereIn('subscription_type', ['pro', 'premium']);
    }

    // ── Helpers ───────────────────────────────────────────────────

    public function isOpenNow(): bool
    {
        if ($this->is_open_24h) return true;
        if (!$this->opens_at || !$this->closes_at) return false;
        $now = now()->format('H:i:s');
        return $now >= $this->opens_at && $now <= $this->closes_at;
    }

    public function getPriceForFuelType(string $type): ?float
    {
        return $this->fuelPrices
            ->where('fuel_type', $type)
            ->where('is_available', true)
            ->first()
            ?->price;
    }
}
