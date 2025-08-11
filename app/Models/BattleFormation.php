<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BattleFormation extends Model
{
    use HasFactory, HasTranslations;

    public $timestamps = false;

    protected $fillable = [
        'slug',
        'points',
        'warhammer_id',
    ];

    public function translations()
    {
        return $this->hasMany(BattleFormationTranslation::class)->orderBy("lang_id", "asc");
    }
}