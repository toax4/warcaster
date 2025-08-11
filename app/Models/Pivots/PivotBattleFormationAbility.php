<?php

namespace App\Models\Pivots;

use App\Traits\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PivotBattleFormationAbility extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'pivot_battle_formation_ability';

    protected $primaryKey = ['ability_id', 'battle_formation_id'];

    public $timestamps = false;

    protected $fillable = [
        'ability_id',
        'battle_formation_id',
    ];
}