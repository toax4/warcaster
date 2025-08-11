<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weapon extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'slug',
        'range',
        'attack',
        'hit',
        'wound',
        'rend',
        'damage',
        'warhammer_id',
    ];
    public $timestamps = false;

    public function translations()
    {
        return $this->hasMany(WeaponTranslation::class)->orderBy("lang_id", "asc");
    }

    public function abilities()
    {
        return $this->belongsToMany(WeaponAbility::class, 'pivot_weapon_weapon_abilitiy')->orderBy("displayOrder", "asc")->orderBy('slug', 'asc');
    }

    public function withAbilities()
    {
        $this->abilities = $this->abilities()->orderBy("displayOrder", "asc")->get();
    }
}