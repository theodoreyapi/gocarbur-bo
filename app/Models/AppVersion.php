<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    protected $primaryKey = 'id_app_version';

    protected $fillable = [
        'platform',
        'version',
        'build_number',
        'force_update',
        'store_url',
        'changelog',
        'is_current',
        'released_at',
    ];

    protected $casts = [
        'is_current'  => 'boolean',
        'released_at' => 'date',
    ];

    /* ── Scopes ──────────────────────────────────── */

    public function scopeCurrent($q)
    {
        return $q->where('is_current', true);
    }
    public function scopeForPlatform($q, string $p)
    {
        return $q->where('platform', $p);
    }

    /* ── Static helpers ──────────────────────────── */

    public static function platforms(): array
    {
        return ['android', 'ios'];
    }

    public static function releaseTypes(): array
    {
        return ['major' => 'Major', 'minor' => 'Minor', 'patch' => 'Patch'];
    }

    public static function forceUpdateLabels(): array
    {
        return [
            'none'     => 'Aucune',
            'optional' => 'Recommandée',
            'required' => 'Forcée',
        ];
    }

    /* ── Helpers ─────────────────────────────────── */

    /**
     * Infer release type from semver: major.minor.patch
     */
    public function getReleaseTypeAttribute(): string
    {
        $parts = explode('.', $this->version);
        if (isset($parts[2]) && (int)$parts[2] > 0) return 'patch';
        if (isset($parts[1]) && (int)$parts[1] > 0) return 'minor';
        return 'major';
    }

    public function getReleaseTypeBadgeAttribute(): string
    {
        return match ($this->release_type) {
            'major' => 'badge-warning',
            'minor' => 'badge-info',
            'patch' => 'badge-gray',
            default => 'badge-gray',
        };
    }

    public function getForceUpdateLabelAttribute(): string
    {
        return self::forceUpdateLabels()[$this->force_update] ?? $this->force_update;
    }

    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_current) return 'badge-success';
        if ($this->force_update === 'required') return 'badge-danger';
        return 'badge-gray';
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_current) return 'Actuelle';
        if ($this->force_update === 'required') return 'Force update';
        return 'Ancienne';
    }

    public function getPlatformIconAttribute(): string
    {
        return match ($this->platform) {
            'android' => 'fa-brands fa-android',
            'ios'     => 'fa-brands fa-apple',
            default   => 'fa-solid fa-mobile',
        };
    }

    public function getPlatformColorAttribute(): string
    {
        return match ($this->platform) {
            'android' => '#10B981',
            'ios'     => '#3B82F6',
            default   => 'var(--text-muted)',
        };
    }
}
