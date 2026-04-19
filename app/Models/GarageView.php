<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GarageView extends Model
{
    protected $table      = 'garage_views';
    protected $primaryKey = 'id_gara_view';
    public    $timestamps = false;

    protected $fillable = ['garage_id', 'user_id', 'ip_address', 'action', 'viewed_at'];

    protected $casts = ['viewed_at' => 'datetime'];
}
