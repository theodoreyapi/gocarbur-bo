<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id','reviewable_type','reviewable_id','rating','comment','is_approved','approved_at'];
    protected $casts = ['is_approved'=>'boolean','approved_at'=>'datetime'];
    public function user()       { return $this->belongsTo(User::class); }
    public function reviewable() { return $this->morphTo(); }
}
