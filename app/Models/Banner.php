<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use SoftDeletes;

    protected $table      = 'banners';
    protected $primaryKey = 'id_banner';

    protected $fillable = [
        'title', 'image_url', 'action_url', 'position',
        'target_type', 'target_city', 'advertiser_name',
        'price_paid', 'starts_at', 'ends_at', 'is_active',
        'impressions_count', 'clicks_count',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'starts_at'  => 'date',
        'ends_at'    => 'date',
        'price_paid' => 'float',
    ];
}
