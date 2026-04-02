<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelPrice extends Model
{
    protected $fillable = ['station_id', 'fuel_type', 'price', 'is_available', 'updated_at_price'];
    protected $casts = ['is_available' => 'boolean', 'price' => 'decimal:2', 'updated_at_price' => 'datetime'];

    public function station() { return $this->belongsTo(Station::class); }
}
