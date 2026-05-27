<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $primaryKey = 'id_notification';

    protected $fillable = [
        'user_id', 'type', 'title', 'body', 'icon',
        'data', 'action_url', 'is_read', 'read_at',
        'is_push_sent', 'push_sent_at',
    ];

    protected $casts = [
        'data'         => 'array',
        'is_read'      => 'boolean',
        'is_push_sent' => 'boolean',
        'read_at'      => 'datetime',
        'push_sent_at' => 'datetime',
    ];

    /* ── Relations ───────────────────────────────── */

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserCarbur::class, 'user_id', 'id_user_carbu');
    }

    /* ── Scopes ──────────────────────────────────── */

    public function scopeUnread($query)   { return $query->where('is_read', false); }
    public function scopePushSent($query) { return $query->where('is_push_sent', true); }
    public function scopeByType($query, string $type) { return $query->where('type', $type); }

    /* ── Static helpers ──────────────────────────── */

    public static function typeLabels(): array
    {
        return [
            'document_expiry' => 'Expiration document',
            'fuel_alert'      => 'Alerte carburant',
            'promotion'       => 'Promotion',
            'reminder'        => 'Rappel',
            'system'          => 'Système',
            'conseil'         => 'Conseil',
            'broadcast'       => 'Broadcast',
        ];
    }

    public static function typeIcons(): array
    {
        return [
            'document_expiry' => 'fa-file-circle-exclamation',
            'fuel_alert'      => 'fa-gas-pump',
            'promotion'       => 'fa-tag',
            'reminder'        => 'fa-bell',
            'system'          => 'fa-gear',
            'conseil'         => 'fa-lightbulb',
            'broadcast'       => 'fa-bullhorn',
        ];
    }

    public static function typeColors(): array
    {
        return [
            'document_expiry' => 'var(--danger)',
            'fuel_alert'      => 'var(--warning)',
            'promotion'       => 'var(--success)',
            'reminder'        => 'var(--info)',
            'system'          => 'var(--text-muted)',
            'conseil'         => '#8B5CF6',
            'broadcast'       => 'var(--primary)',
        ];
    }

    /* ── Accessors ───────────────────────────────── */

    public function getTypeLabelAttribute(): string
    {
        return self::typeLabels()[$this->type] ?? $this->type;
    }

    public function getTypeIconAttribute(): string
    {
        return self::typeIcons()[$this->type] ?? 'fa-bell';
    }

    public function getTypeColorAttribute(): string
    {
        return self::typeColors()[$this->type] ?? 'var(--primary)';
    }
}
