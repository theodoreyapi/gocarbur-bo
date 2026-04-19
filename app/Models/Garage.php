<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
    ];

    protected $casts = [
        'photos'                  => 'array',
        'is_open_24h'             => 'boolean',
        'is_verified'             => 'boolean',
        'is_active'               => 'boolean',
        'subscription_expires_at' => 'datetime',
        'latitude'                => 'float',
        'longitude'               => 'float',
        'rating'                  => 'float',
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
}
