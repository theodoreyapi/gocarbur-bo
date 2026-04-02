<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_id', 'type', 'number',
        'issue_date', 'expiry_date',
        'file_url', 'file_path', 'status', 'notes',
    ];

    protected $casts = [
        'issue_date'  => 'date',
        'expiry_date' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    // Recalcule le statut avant sauvegarde
    protected static function booted(): void
    {
        static::saving(function (Document $doc) {
            if ($doc->expiry_date) {
                $days = now()->diffInDays($doc->expiry_date, false);
                $doc->status = match (true) {
                    $days < 0  => 'expired',
                    $days <= 30 => 'expiring_soon',
                    default    => 'valid',
                };
            }
        });
    }
}
