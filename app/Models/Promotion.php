<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use SoftDeletes;

    protected $table      = 'promotions';
    protected $primaryKey = 'id_promotion';

    protected $fillable = [
        'promotable_type', 'promotable_id', 'title', 'description',
        'image_url', 'type', 'discount_percent', 'discount_amount',
        'starts_at', 'ends_at', 'send_push_notification',
        'notification_radius_km', 'is_active',
    ];

    protected $casts = [
        'send_push_notification' => 'boolean',
        'is_active'              => 'boolean',
        'starts_at'              => 'date',
        'ends_at'                => 'date',
        'discount_percent'       => 'float',
        'discount_amount'        => 'float',
    ];

    public function promotable(): MorphTo
    {
        return $this->morphTo();
    }
}
