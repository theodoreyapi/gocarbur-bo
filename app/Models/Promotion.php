<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'promotable_type', 'promotable_id',
        'title', 'description', 'image_url', 'type',
        'discount_percent', 'discount_amount',
        'starts_at', 'ends_at',
        'send_push_notification', 'notification_radius_km', 'is_active',
    ];

    protected $casts = [
        'starts_at'               => 'date',
        'ends_at'                 => 'date',
        'send_push_notification'  => 'boolean',
        'is_active'               => 'boolean',
        'discount_percent'        => 'decimal:2',
        'discount_amount'         => 'decimal:2',
    ];

    public function promotable()
    {
        return $this->morphTo();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }
}
