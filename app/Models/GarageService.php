<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GarageService extends Model
{

    public const TYPES = [
        'vidange',
        'freins',
        'pneus',
        'batterie',
        'climatisation',
        'electricite',
        'carrosserie',
        'vitrage',
        'courroie_distribution',
        'amortisseurs',
        'echappement',
        'revision_complete',
        'diagnostic_electronique',
        'depannage_route',
        'remorquage',
        'lavage_interieur',
        'lavage_exterieur',
        'polissage',
    ];

    protected $table      = 'garage_services';

    protected $primaryKey = 'id_gara_service';

    protected $fillable = ['garage_id', 'service', 'price_range'];

    public function garage(): BelongsTo
    {
        return $this->belongsTo(Garage::class, 'garage_id', 'id_garage');
    }
}
