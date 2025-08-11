<?php

namespace App\Models\Pivots;

use App\Traits\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PivotUnitWeapon extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'pivot_unit_weapon';

    protected $primaryKey = ['weapon_id', 'unit_id'];

    public $timestamps = false;

    protected $fillable = [
        'weapon_id',
        'unit_id',
    ];
}
