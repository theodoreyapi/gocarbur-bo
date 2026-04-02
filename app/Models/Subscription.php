<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'subscribable_type','subscribable_id','plan','amount','billing_cycle',
        'starts_at','expires_at','status','payment_method',
        'payment_reference','payment_transaction_id','paid_at','cancellation_reason','cancelled_at',
    ];

    protected $casts = [
        'starts_at'   => 'date',
        'expires_at'  => 'date',
        'paid_at'     => 'datetime',
        'cancelled_at'=> 'datetime',
        'amount'      => 'decimal:2',
    ];

    public function subscribable() { return $this->morphTo(); }

    public function scopeActive($q)
    {
        return $q->where('status','active')->where('expires_at','>=',now());
    }
}
