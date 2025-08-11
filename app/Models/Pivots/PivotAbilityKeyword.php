<?php

namespace App\Models\Pivots;

use App\Traits\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PivotAbilityKeyword extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'pivot_ability_keyword';

    protected $primaryKey = ['ability_id', 'keyword_id'];

    public $timestamps = false;

    protected $fillable = [
        'ability_id',
        'keyword_id',
    ];
}