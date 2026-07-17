<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelPrice extends Model
{
    protected $primaryKey = 'id_fuel_price';

    protected $fillable = ['fuel_type', 'price', 'is_available', 'updated_at_price', 'station_id'];

    protected $casts = [
        'is_available' => 'boolean',
        'updated_at_price' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class, 'station_id', 'id_station');
    }
}
