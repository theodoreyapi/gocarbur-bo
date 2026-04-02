<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = [
        'user_id','vehicle_id','document_id','type','title','notes',
        'remind_at','remind_before_days','is_sent','sent_at',
        'is_dismissed','is_recurring','recurrence',
    ];
    protected $casts = [
        'remind_at'    => 'date',
        'is_sent'      => 'boolean',
        'is_dismissed' => 'boolean',
        'is_recurring' => 'boolean',
        'sent_at'      => 'datetime',
    ];
    public function user()     { return $this->belongsTo(User::class); }
    public function vehicle()  { return $this->belongsTo(Vehicle::class)->withDefault(); }
    public function document() { return $this->belongsTo(Document::class)->withDefault(); }
}
