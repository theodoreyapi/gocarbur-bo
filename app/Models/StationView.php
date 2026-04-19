<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StationView extends Model
{
    protected $table      = 'station_views';
    protected $primaryKey = 'id_sta_view';
    public    $timestamps = false;

    protected $fillable = ['station_id', 'user_id', 'ip_address', 'action', 'viewed_at'];

    protected $casts = ['viewed_at' => 'datetime'];
}
