<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserCarbur extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /** @var string Nom de la table */
    protected $table = 'users_carbur';

    /** @var string Clé primaire personnalisée */
    protected $primaryKey = 'id_user_carbu';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'city',
        'avatar_url',
        'password',
        'token',
        'subscription_type',
        'subscription_expires_at',
        'fcm_token',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'token',
        'remember_token',
    ];

    protected $casts = [
        'is_active'               => 'boolean',
        'subscription_expires_at' => 'datetime',
        'last_login_at'           => 'datetime',
    ];
}
