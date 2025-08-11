<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitAbility extends Ability
{
    public function units()
    {
        return $this->belongsToMany(Unit::class, 'pivot_unit_ability', 'ability_id')->orderBy('slug', 'asc');
    }
}