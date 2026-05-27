<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id_banner';

    protected $fillable = [
        'title',
        'image_url',
        'action_url',
        'position',
        'target_type',
        'target_city',
        'advertiser_name',
        'price_paid',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at'   => 'date',
        'is_active' => 'boolean',
        'price_paid' => 'decimal:2',
    ];

    /* ── Scopes ─────────────────────────────────── */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /* ── Helpers ─────────────────────────────────── */

    public function getCtrAttribute(): float
    {
        if ($this->impressions_count === 0) return 0;
        return round(($this->clicks_count / $this->impressions_count) * 100, 1);
    }

    public static function positionLabels(): array
    {
        return [
            'home_top'      => 'Bannière accueil principale',
            'home_middle'   => 'Bannière accueil secondaire',
            'map_bottom'    => 'Carte — bas',
            'stations_list' => 'Liste stations',
            'garages_list'  => 'Liste garages',
            'articles_list' => 'Entre articles conseils',
            'splash'        => 'Splash screen',
        ];
    }

    public static function targetLabels(): array
    {
        return [
            'all'           => 'Tous les utilisateurs',
            'free_users'    => 'Utilisateurs gratuits uniquement',
            'premium_users' => 'Utilisateurs Premium uniquement',
            'city'          => 'Par ville',
        ];
    }

    public function getPositionLabelAttribute(): string
    {
        return self::positionLabels()[$this->position] ?? $this->position;
    }

    public function getTargetLabelAttribute(): string
    {
        $label = self::targetLabels()[$this->target_type] ?? $this->target_type;
        if ($this->target_type === 'city' && $this->target_city) {
            $label .= ' — ' . $this->target_city;
        }
        return $label;
    }
}
