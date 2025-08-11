<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeaponAbility extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'slug',
        'warhammer_id',
    ];

    public $timestamps = false;

    public function translations()
    {
        return $this->hasMany(WeaponAbilityTranslation::class)->orderBy("lang_id", "asc");
    }
}