<?php

namespace App\Models\Pivots;

use App\Traits\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Model;

class PivotWeaponWeaponAbility extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'pivot_weapon_weapon_abilitiy';

    protected $primaryKey = ['weapon_id', 'weapon_ability_id'];

    public $timestamps = false;

    protected $fillable = [
        'weapon_id',
        'weapon_ability_id',
        'displayOrder',
    ];
}
