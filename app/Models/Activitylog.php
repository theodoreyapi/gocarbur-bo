<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $primaryKey = 'id_log';

    protected $fillable = [
        'causer_type', 'causer_id',
        'subject_type', 'subject_id',
        'action', 'description',
        'old_values', 'new_values',
        'ip_address', 'user_agent',
        'occurred_at',
    ];

    protected $casts = [
        'old_values'  => 'array',
        'new_values'  => 'array',
        'occurred_at' => 'datetime',
    ];

    /* ── Relations ───────────────────────────────── */

    public function causer(): MorphTo  { return $this->morphTo(); }
    public function subject(): MorphTo { return $this->morphTo(); }

    /* ── Scopes ──────────────────────────────────── */

    public function scopeForAction($q, string $action) { return $q->where('action', 'like', "{$action}%"); }
    public function scopeForCauser($q, $id)            { return $q->where('causer_id', $id); }
    public function scopeToday($q)    { return $q->whereDate('occurred_at', today()); }
    public function scopeThisWeek($q) { return $q->where('occurred_at', '>=', now()->startOfWeek()); }
    public function scopeThisMonth($q){ return $q->whereMonth('occurred_at', now()->month); }

    /* ── Static helpers ──────────────────────────── */

    public static function actionGroups(): array
    {
        return [
            'user'         => 'Gestion utilisateurs',
            'station'      => 'Gestion stations',
            'garage'       => 'Gestion garages',
            'article'      => 'Gestion articles',
            'subscription' => 'Abonnements',
            'payment'      => 'Paiements',
            'setting'      => 'Paramètres',
            'auth'         => 'Authentification',
            'system'       => 'Système',
            'review'       => 'Avis',
            'banner'       => 'Bannières',
        ];
    }

    public static function levelConfig(): array
    {
        return [
            // action keyword => [level, badge, label]
            'login'     => ['success', 'badge-success', 'Succès'],
            'created'   => ['success', 'badge-success', 'Succès'],
            'approved'  => ['success', 'badge-success', 'Succès'],
            'verified'  => ['success', 'badge-success', 'Succès'],
            'activated' => ['success', 'badge-success', 'Succès'],
            'published' => ['info',    'badge-info',    'Info'],
            'updated'   => ['info',    'badge-info',    'Info'],
            'viewed'    => ['info',    'badge-info',    'Info'],
            'exported'  => ['info',    'badge-info',    'Info'],
            'suspended' => ['warning', 'badge-warning', 'Attention'],
            'cancelled' => ['warning', 'badge-warning', 'Attention'],
            'disabled'  => ['warning', 'badge-warning', 'Attention'],
            'deleted'   => ['danger',  'badge-danger',  'Erreur'],
            'failed'    => ['danger',  'badge-danger',  'Erreur'],
            'error'     => ['danger',  'badge-danger',  'Erreur'],
        ];
    }

    /* ── Accessors ───────────────────────────────── */

    public function getCauserNameAttribute(): string
    {
        if (!$this->causer) return 'Système';
        return $this->causer->name ?? ($this->causer->email ?? '—');
    }

    public function getCauserInitialsAttribute(): string
    {
        $name = $this->causer_name;
        if ($name === 'Système') return '🤖';
        $words = explode(' ', trim($name));
        return strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
    }

    public function getLevelBadgeAttribute(): string
    {
        foreach (self::levelConfig() as $keyword => $config) {
            if (str_contains(strtolower($this->action), $keyword)) return $config[1];
        }
        return 'badge-info';
    }

    public function getLevelLabelAttribute(): string
    {
        foreach (self::levelConfig() as $keyword => $config) {
            if (str_contains(strtolower($this->action), $keyword)) return $config[2];
        }
        return 'Info';
    }

    public function getActionGroupAttribute(): string
    {
        foreach (array_keys(self::actionGroups()) as $group) {
            if (str_starts_with(strtolower($this->action), $group . '_')
                || str_contains(strtolower($this->action), '_' . $group)) {
                return $group;
            }
        }
        // Essaie via subject_type
        if ($this->subject_type) {
            $model = strtolower(class_basename($this->subject_type));
            if (isset(self::actionGroups()[$model])) return $model;
        }
        return 'system';
    }

    /* ── Helper statique pour logger ────────────────── */

    public static function log(
        $causer,
        string $action,
        ?string $description = null,
        $subject = null,
        array $oldValues = [],
        array $newValues = []
    ): self {
        return self::create([
            'causer_type'  => $causer ? get_class($causer) : null,
            'causer_id'    => $causer?->getKey(),
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->getKey(),
            'action'       => $action,
            'description'  => $description,
            'old_values'   => $oldValues ?: null,
            'new_values'   => $newValues ?: null,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
            'occurred_at'  => now(),
        ]);
    }
}
