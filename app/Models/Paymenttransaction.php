<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $primaryKey = 'id_pay_transac';

    protected $fillable = [
        'reference', 'payer_type', 'payer_id', 'subscription_id',
        'amount', 'currency', 'payment_method', 'status',
        'operator_reference', 'operator_transaction_id', 'operator_response',
        'phone_payer', 'failure_reason', 'paid_at',
    ];

    protected $casts = [
        'amount'            => 'decimal:2',
        'operator_response' => 'array',
        'paid_at'           => 'datetime',
    ];

    /* ── Relations ───────────────────────────────── */

    public function payer(): MorphTo
    {
        return $this->morphTo();
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_id', 'id_subcrip');
    }

    /* ── Scopes ──────────────────────────────────── */

    public function scopeSuccess($q)  { return $q->where('status', 'success'); }
    public function scopeFailed($q)   { return $q->where('status', 'failed'); }
    public function scopePending($q)  { return $q->where('status', 'pending'); }
    public function scopeInPeriod($q, int $days) {
        return $q->where('created_at', '>=', now()->subDays($days));
    }

    /* ── Static helpers ──────────────────────────── */

    public static function methodLabels(): array
    {
        return [
            'orange_money' => 'Orange Money',
            'mtn_money'    => 'MTN MoMo',
            'moov_money'   => 'Moov Money',
            'wave'         => 'Wave',
            'cinetpay'     => 'CinetPay',
            'especes'      => 'Espèces',
            'virement'     => 'Virement',
        ];
    }

    public static function methodColors(): array
    {
        return [
            'orange_money' => '#FF6600',
            'mtn_money'    => '#FFCC00',
            'moov_money'   => '#00A651',
            'wave'         => '#1CB5E0',
            'cinetpay'     => '#E63946',
            'especes'      => '#6B7280',
            'virement'     => '#3B82F6',
        ];
    }

    /* ── Accessors ───────────────────────────────── */

    public function getPayerNameAttribute(): string
    {
        return $this->payer?->name ?? '—';
    }

    public function getPayerInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->payer_name));
        return strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
    }

    public function getMethodLabelAttribute(): string
    {
        return self::methodLabels()[$this->payment_method] ?? $this->payment_method;
    }

    public function getMethodColorAttribute(): string
    {
        return self::methodColors()[$this->payment_method] ?? '#6B7280';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'success'   => 'badge-success',
            'failed'    => 'badge-danger',
            'pending'   => 'badge-warning',
            'refunded'  => 'badge-info',
            'cancelled' => 'badge-gray',
            default     => 'badge-gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'success'   => 'Succès',
            'failed'    => 'Échoué',
            'pending'   => 'En attente',
            'refunded'  => 'Remboursé',
            'cancelled' => 'Annulé',
            default     => $this->status,
        };
    }

    public function getAmountColorAttribute(): string
    {
        return match ($this->status) {
            'success'  => 'var(--success)',
            'failed'   => 'var(--danger)',
            'refunded' => 'var(--info)',
            default    => 'var(--text-muted)',
        };
    }

    public function getPlanLabelAttribute(): ?string
    {
        return $this->subscription?->plan_label;
    }

    public function getPlanBadgeAttribute(): ?string
    {
        return $this->subscription?->plan_badge;
    }
}
