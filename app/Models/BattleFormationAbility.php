<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BattleFormationAbility extends Ability
{
    public function battleFormations()
    {
        return $this->belongsToMany(BattleFormation::class, 'pivot_battle_formation_ability', 'ability_id')->orderBy('slug', 'asc');
    }
}