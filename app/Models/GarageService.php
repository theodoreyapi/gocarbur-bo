<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GarageService extends Model {
    protected $table      = 'garage_services';
    protected $primaryKey = 'id_gara_service';

    protected $fillable = ['garage_id', 'service', 'price_range'];

    public function garage(): BelongsTo
    {
        return $this->belongsTo(Garage::class, 'garage_id', 'id_garage');
    }
}
