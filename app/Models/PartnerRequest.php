<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerRequest extends Model
{
    protected $table      = 'partner_requests';
    protected $primaryKey = 'id_demande';

    protected $fillable = [
        'type',
        'business_name',
        'contact_name',
        'contact_phone',
        'contact_email',
        'address',
        'city',
        'latitude',
        'longitude',
        'message',
        'status',
        'admin_notes',
        'admin_id',
        'processed_at',
    ];

    protected $casts = [
        'latitude'     => 'float',
        'longitude'    => 'float',
        'processed_at' => 'datetime',
    ];
}
