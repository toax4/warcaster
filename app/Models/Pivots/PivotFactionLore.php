<?php

namespace App\Models\Pivots;

use App\Traits\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PivotFactionLore extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'pivot_lore_faction';

    protected $primaryKey = ['lore_id', 'faction_id'];

    public $timestamps = false;

    protected $fillable = [
        'lore_id',
        'faction_id',
    ];
}