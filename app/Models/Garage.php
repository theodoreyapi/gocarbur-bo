<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Garage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'type', 'address', 'city', 'country',
        'latitude', 'longitude', 'phone', 'whatsapp',
        'logo_url', 'photos', 'opens_at', 'closes_at',
        'is_open_24h', 'is_verified', 'subscription_type',
        'subscription_expires_at', 'views_count', 'is_active',
        'description', 'rating', 'rating_count',
    ];

    protected $casts = [
        'photos'                  => 'array',
        'is_open_24h'             => 'boolean',
        'is_verified'             => 'boolean',
        'is_active'               => 'boolean',
        'subscription_expires_at' => 'datetime',
        'latitude'                => 'decimal:7',
        'longitude'               => 'decimal:7',
        'rating'                  => 'decimal:2',
    ];

    public function services()  { return $this->hasMany(GarageService::class); }
    public function promotions(){ return $this->morphMany(Promotion::class, 'promotable'); }
    public function reviews()   { return $this->morphMany(Review::class, 'reviewable')->where('is_approved', true); }
    public function favorites() { return $this->morphMany(Favorite::class, 'favoriteable'); }
    public function views()     { return $this->hasMany(GarageView::class); }

    public function scopeActive($q)  { return $q->where('is_active', true); }
    public function scopeVerified($q){ return $q->where('is_verified', true); }

    public function scopeNearby($query, float $lat, float $lng, int $radius = 5)
    {
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

    public function isOpenNow(): bool
    {
        if ($this->is_open_24h) return true;
        if (!$this->opens_at || !$this->closes_at) return false;
        $now = now()->format('H:i:s');
        return $now >= $this->opens_at && $now <= $this->closes_at;
    }
}
