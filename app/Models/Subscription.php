<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Carbon\Carbon;

class Subscription extends Model
{
    protected $primaryKey = 'id_subcrip';

    protected $fillable = [
        'subscribable_type',
        'subscribable_id',
        'plan',
        'amount',
        'billing_cycle',
        'starts_at',
        'expires_at',
        'status',
        'payment_method',
        'payment_reference',
        'payment_transaction_id',
        'paid_at',
        'cancellation_reason',
        'cancelled_at',
    ];

    protected $casts = [
        'starts_at'      => 'date',
        'expires_at'     => 'date',
        'paid_at'        => 'datetime',
        'cancelled_at'   => 'datetime',
        'amount'         => 'decimal:2',
    ];

    /* ── Relations ───────────────────────────────── */

    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }

    /* ── Scopes ──────────────────────────────────── */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('status', 'active')
                     ->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    public function scopeByPlanGroup($query, string $group)
    {
        return match ($group) {
            'users'    => $query->whereIn('plan', ['user_free', 'user_premium']),
            'stations' => $query->whereIn('plan', ['station_free', 'station_pro', 'station_premium']),
            'garages'  => $query->whereIn('plan', ['garage_free', 'garage_pro', 'garage_premium']),
            default    => $query,
        };
    }

    /* ── Static helpers ──────────────────────────── */

    public static function planLabels(): array
    {
        return [
            'user_free'       => 'Gratuit',
            'user_premium'    => 'User Premium',
            'station_free'    => 'Station Gratuit',
            'station_pro'     => 'Station Pro',
            'station_premium' => 'Station Premium',
            'garage_free'     => 'Garage Gratuit',
            'garage_pro'      => 'Garage Pro',
            'garage_premium'  => 'Garage Premium',
        ];
    }

    public static function planPrices(): array
    {
        return [
            'user_premium'    => 1500,
            'station_pro'     => 12500,
            'station_premium' => 32500,
            'garage_pro'      => 12500,
            'garage_premium'  => 32500,
        ];
    }

    public static function paymentMethodLabels(): array
    {
        return [
            'orange_money' => 'Orange Money',
            'mtn_money'    => 'MTN MoMo',
            'moov_money'   => 'Moov Money',
            'cinetpay'     => 'CinetPay',
            'wave'         => 'Wave',
            'especes'      => 'Espèces',
        ];
    }

    public static function paymentMethodColors(): array
    {
        return [
            'orange_money' => '#FF6600',
            'mtn_money'    => '#FFCC00',
            'moov_money'   => '#00ADEF',
            'cinetpay'     => '#E63946',
            'wave'         => '#1CB5E0',
            'especes'      => '#6B7280',
        ];
    }

    /* ── Accessors ───────────────────────────────── */

    public function getPlanLabelAttribute(): string
    {
        return self::planLabels()[$this->plan] ?? $this->plan;
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return self::paymentMethodLabels()[$this->payment_method] ?? $this->payment_method ?? '—';
    }

    public function getPaymentMethodColorAttribute(): string
    {
        return self::paymentMethodColors()[$this->payment_method] ?? '#6B7280';
    }

    public function getSubscriberNameAttribute(): string
    {
        return $this->subscribable?->name ?? '—';
    }

    public function getSubscriberTypeAttribute(): string
    {
        return match ($this->subscribable_type) {
            'App\\Models\\UserCarbur' => 'user',
            'App\\Models\\Station'   => 'station',
            'App\\Models\\Garage'    => 'garage',
            default                  => 'unknown',
        };
    }

    public function getSubscriberInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->subscriber_name));
        return strtoupper(
            substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : '')
        );
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->status === 'active'
            && $this->expires_at
            && $this->expires_at->diffInDays(now()) <= 7
            && $this->expires_at->isFuture();
    }

    public function getPlanGroupAttribute(): string
    {
        return str_starts_with($this->plan, 'user_')
            ? 'user'
            : (str_starts_with($this->plan, 'station_') ? 'station' : 'garage');
    }

    public function getPlanBadgeAttribute(): string
    {
        if (str_ends_with($this->plan, '_premium')) return 'badge-purple';
        if (str_ends_with($this->plan, '_pro'))     return 'badge-info';
        return 'badge-gray';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active'    => $this->is_expiring_soon ? 'badge-warning' : 'badge-success',
            'expired'   => 'badge-gray',
            'cancelled' => 'badge-danger',
            'pending'   => 'badge-warning',
            'failed'    => 'badge-danger',
            default     => 'badge-gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->status === 'active' && $this->is_expiring_soon) {
            return 'Expire bientôt';
        }
        return match ($this->status) {
            'active'    => 'Actif',
            'expired'   => 'Expiré',
            'cancelled' => 'Annulé',
            'pending'   => 'En attente',
            'failed'    => 'Échoué',
            default     => $this->status,
        };
    }
}
