<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GarageService extends Model {
    protected $fillable = ['garage_id','service','price_range'];
    public function garage(){ return $this->belongsTo(Garage::class); }
}

// ────────────────────────────────────────────────────────────────
