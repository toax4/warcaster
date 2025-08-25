<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ability extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'abilities';

    protected $fillable = [
        'phase_id',
        'phase_detail_id',
        'slug',
        'cp_cost',
        'points',
        'warhammer_id',
    ];
    
    public $timestamps = false;

    public function translations()
    {
        return $this->hasMany(AbilityTranslation::class)->orderBy("lang_id", "asc");
    }

    public function phase()
    {
        return $this->belongsTo(Phase::class, 'phase_id');
    }

    public function phaseDetail()
    {
        return $this->belongsTo(PhaseDetail::class, 'phase_detail_id');
    }
}