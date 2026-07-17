<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Station extends Model
{
    use SoftDeletes;

    protected $table      = 'stations';
    protected $primaryKey = 'id_station';

    protected $fillable = [
        'name',
        'brand',
        'address',
        'city',
        'country',
        'latitude',
        'longitude',
        'phone',
        'whatsapp',
        'logo_url',
        'photos',
        'opens_at',
        'closes_at',
        'is_open_24h',
        'is_verified',
        'subscription_type',
        'subscription_expires_at',
        'views_count',
        'is_active',
        'description',
        'owner_id',
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

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    // UserCarbur, Station, Garage
    public function subscriptions(): MorphMany
    {
        return $this->morphMany(Subscription::class, 'subscribable');
    }

    public function activeSubscription(): MorphOne
    {
        return $this->morphOne(Subscription::class, 'subscribable')
            ->where('status', 'active')
            ->latest('starts_at');
    }


    // Propriétaire principal (FK directe owner_id)
    public function owner()
    {
        return $this->belongsTo(StationOwner::class, 'owner_id', 'id_station_owner');
    }

    public function fuelPrices()
    {
        return $this->hasMany(FuelPrice::class, 'station_id', 'id_station');
    }

    // Équipe (managers/employees + propriétaire) via la table pivot station_owner_station
    public function team()
    {
        return $this->belongsToMany(
            StationOwner::class,
            'station_owner_station',
            'station_id',
            'owner_id',
            'id_station',
            'id_station_owner'
        )->withPivot('id_stat_owner_stat', 'role')->withTimestamps();
    }
}
