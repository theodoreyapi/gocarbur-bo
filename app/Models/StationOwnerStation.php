<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StationOwnerStation extends Model
{
    protected $table = 'station_owner_station';

    protected $primaryKey = 'id_stat_owner_stat';

    protected $fillable = ['owner_id', 'station_id', 'role'];

    public function owner()
    {
        return $this->belongsTo(StationOwner::class, 'owner_id', 'id_station_owner');
    }

    public function station()
    {
        return $this->belongsTo(Station::class, 'station_id', 'id_station');
    }
}
