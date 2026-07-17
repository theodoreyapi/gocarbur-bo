<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GarageOwnerGarage extends Model
{
    protected $table = 'garage_owner_garage';

    protected $primaryKey = 'id_gara_owner_gara';

    protected $fillable = ['owner_id', 'garage_id', 'role'];

    public function owner()
    {
        return $this->belongsTo(GarageOwner::class, 'owner_id', 'id_gara_owner');
    }

    public function garage()
    {
        return $this->belongsTo(Garage::class, 'garage_id', 'id_garage');
    }
}
