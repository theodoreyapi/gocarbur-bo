<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class StationOwner extends Model
{
    use SoftDeletes, Notifiable;

    protected $primaryKey = 'id_station_owner';

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'company_name', 'rccm',
        'status', 'is_active', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // Stations dont il est le propriétaire principal (owner_id direct)
    public function stations()
    {
        return $this->hasMany(Station::class, 'owner_id', 'id_station_owner');
    }

    // Garages dont il est le propriétaire principal (owner_id direct)
    public function garages()
    {
        return $this->hasMany(Garage::class, 'owner_id', 'id_station_owner');
    }

    // Stations où il intervient comme manager/employee/owner via la table pivot
    public function teamStations()
    {
        return $this->belongsToMany(
            Station::class,
            'station_owner_station',
            'owner_id',
            'station_id',
            'id_station_owner',
            'id_station'
        )->withPivot('id_stat_owner_stat', 'role')->withTimestamps();
    }
}
