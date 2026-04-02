<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id','type','title','body','icon',
        'data','action_url','is_read','read_at',
        'is_push_sent','push_sent_at',
    ];

    protected $casts = [
        'data'         => 'array',
        'is_read'      => 'boolean',
        'is_push_sent' => 'boolean',
        'read_at'      => 'datetime',
        'push_sent_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
