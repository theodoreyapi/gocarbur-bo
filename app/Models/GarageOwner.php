<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class GarageOwner extends Model
{
    use SoftDeletes, Notifiable;

    protected $primaryKey = 'id_gara_owner';

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'company_name', 'rccm',
        'status', 'is_active', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // Garages dont il est le propriétaire principal (owner_id direct)
    public function garages()
    {
        return $this->hasMany(Garage::class, 'owner_id', 'id_gara_owner');
    }

    // Garages où il intervient comme manager/employee/owner via la pivot
    public function teamGarages()
    {
        return $this->belongsToMany(
            Garage::class,
            'garage_owner_garage',
            'owner_id',
            'garage_id',
            'id_gara_owner',
            'id_garage'
        )->withPivot('id_gara_owner_gara', 'role')->withTimestamps();
    }
}
