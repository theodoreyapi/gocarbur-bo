<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Station extends Model
{
    use SoftDeletes;

    protected $table      = 'stations';
    protected $primaryKey = 'id_station';

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
        'latitude'                => 'float',
        'longitude'               => 'float',
    ];

    public function services(): HasMany
    {
        return $this->hasMany(StationService::class, 'station_id', 'id_station');
    }

    public function promotions(): MorphMany
    {
        return $this->morphMany(Promotion::class, 'promotable');
    }

    public function activeFuelPrices(): HasMany
    {
        return $this->hasMany(FuelPrice::class, 'station_id', 'id_station');
    }

    public function views(): HasMany
    {
        return $this->hasMany(StationView::class, 'station_id', 'id_station');
    }
}
