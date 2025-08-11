<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'slug',
        'move',
        'save',
        'control',
        'health',
        'points',
        'bannerImage',
        'rowImage',
        'warhammer_id',
    ];

    public $timestamps = false;

    public function translations()
    {
        return $this->hasMany(UnitTranslation::class)->orderBy("lang_id", "asc");
    }

    public function factions()
    {
        return $this->belongsToMany(Faction::class, 'pivot_unit_faction')->orderBy('slug', 'asc');
    }

    public function abilities()
    {
        return $this->belongsToMany(Ability::class, 'pivot_unit_ability')->orderBy('slug', 'asc');
    }

    public function weapons()
    {
        return $this->belongsToMany(Weapon::class, 'pivot_unit_weapon')->orderBy('slug', 'asc');
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'pivot_unit_keyword')->orderBy('slug', 'asc');
    }
}