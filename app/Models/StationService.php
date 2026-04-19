<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StationService extends Model
{
    protected $table      = 'station_services';
    protected $primaryKey = 'id_sta_service';

    protected $fillable = ['station_id', 'service'];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'station_id', 'id_station');
    }
}
