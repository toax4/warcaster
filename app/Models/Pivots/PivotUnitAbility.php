<?php

namespace App\Models\Pivots;

use App\Traits\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PivotUnitAbility extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'pivot_unit_ability';

    protected $primaryKey = ['ability_id', 'unit_id'];

    public $timestamps = false;

    protected $fillable = [
        'ability_id',
        'unit_id',
    ];
}
