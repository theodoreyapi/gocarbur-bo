<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id_review';

    protected $fillable = [
        'reviewable_type',
        'reviewable_id',
        'user_id',
        'rating',
        'comment',
        'is_approved',
        'approved_at',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'rating'      => 'integer',
    ];

    /* ── Relations ───────────────────────────────── */

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserCarbur::class, 'user_id', 'id_user_carbu');
    }

    /* ── Scopes ──────────────────────────────────── */

    public function scopePending($query)
    {
        return $query->where('is_approved', false)->whereNull('deleted_at');
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /* ── Helpers ─────────────────────────────────── */

    public function getEstablishmentNameAttribute(): string
    {
        return $this->reviewable?->name ?? '—';
    }

    public function getEstablishmentTypeAttribute(): string
    {
        return match ($this->reviewable_type) {
            'App\\Models\\Station' => 'station',
            'App\\Models\\Garage'  => 'garage',
            default                => 'unknown',
        };
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->user?->name ?? '??'));
        return strtoupper(
            (substr($words[0], 0, 1)) . (isset($words[1]) ? substr($words[1], 0, 1) : '')
        );
    }
}
