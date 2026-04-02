<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id', 'station_id', 'fuel_type',
        'liters', 'price_per_liter', 'total_cost',
        'mileage', 'full_tank', 'notes', 'filled_at',
    ];

    protected $casts = [
        'filled_at'       => 'datetime',
        'liters'          => 'decimal:2',
        'price_per_liter' => 'decimal:2',
        'total_cost'      => 'decimal:2',
        'full_tank'       => 'boolean',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class)->withDefault();
    }
}
