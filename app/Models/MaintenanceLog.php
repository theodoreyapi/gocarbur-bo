<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_id', 'type', 'title', 'notes', 'cost',
        'done_at', 'mileage_at_service',
        'next_service_mileage', 'next_service_date', 'garage_name',
    ];

    protected $casts = [
        'done_at'           => 'date',
        'next_service_date' => 'date',
        'cost'              => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
