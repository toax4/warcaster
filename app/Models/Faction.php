<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faction extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'slug',
        'factionHeaderImage',
        'rosterHeaderImage',
        'selectFactionImage',
        'rosterFactionImage',
        'moreInfoImage',
        'parent_id',
        'warhammer_id'
    ];
    
    public $timestamps = false;

    public function translations()
    {
        return $this->hasMany(FactionTranslation::class)->orderBy("lang_id", "asc");
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'faction_unit')->orderBy('slug', 'asc');
    }

    public function battleTraits()
    {
        return $this->belongsToMany(Ability::class, 'battle_traits')->orderBy('slug', 'asc');
    }
}