<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admin_id','title','slug','excerpt','content','cover_image_url',
        'category','is_sponsored','sponsor_name','sponsor_logo_url','sponsor_url',
        'is_published','published_at','views_count','read_time_minutes','tags',
    ];

    protected $casts = [
        'tags'         => 'array',
        'is_sponsored' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function scopePublished($q) { return $q->where('is_published', true); }
    public function scopeSponsored($q) { return $q->where('is_sponsored', true); }

    protected static function booted(): void
    {
        static::creating(function (Article $a) {
            if (!$a->slug) {
                $a->slug = \Str::slug($a->title) . '-' . uniqid();
            }
        });
    }
}
