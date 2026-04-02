<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'brand', 'model', 'year',
        'plate_number', 'fuel_type', 'mileage',
        'color', 'photo_url', 'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'mileage'    => 'integer',
        'year'       => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    public function fuelLogs()
    {
        return $this->hasMany(FuelLog::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function expiringDocuments()
    {
        return $this->documents()
            ->whereIn('status', ['expiring_soon', 'expired'])
            ->orderBy('expiry_date');
    }
}
