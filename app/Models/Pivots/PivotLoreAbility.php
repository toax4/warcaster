<?php

namespace App\Models\Pivots;

use App\Traits\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PivotLoreAbility extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'pivot_lore_ability';

    protected $primaryKey = ['lore_id', 'ability_id'];

    public $timestamps = false;

    protected $fillable = [
        'lore_id',
        'ability_id',
    ];
}