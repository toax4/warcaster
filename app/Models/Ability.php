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
}
