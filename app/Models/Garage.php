<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Garage extends Model
{
    use SoftDeletes;

    protected $table      = 'garages';

    protected $primaryKey = 'id_garage';

    protected $fillable = [
        'name',
        'type',
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
        'rating',
        'rating_count',
        'owner_id',
    ];

    protected $casts = [
        'photos' => 'array',
        'is_open_24h' => 'boolean',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'rating' => 'decimal:2',
    ];

    public function services(): HasMany
    {
        return $this->hasMany(GarageService::class, 'garage_id', 'id_garage');
    }

    public function promotions(): MorphMany
    {
        return $this->morphMany(Promotion::class, 'promotable');
    }

    public function views(): HasMany
    {
        return $this->hasMany(GarageView::class, 'garage_id', 'id_garage');
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

    public function owner()
    {
        return $this->belongsTo(GarageOwner::class, 'owner_id', 'id_gara_owner');
    }

    // Équipe (owner/manager/employee) via la table pivot garage_owner_garage
    public function team()
    {
        return $this->belongsToMany(
            GarageOwner::class,
            'garage_owner_garage',
            'garage_id',
            'owner_id',
            'id_garage',
            'id_gara_owner'
        )->withPivot('id_gara_owner_gara', 'role')->withTimestamps();
    }

}
