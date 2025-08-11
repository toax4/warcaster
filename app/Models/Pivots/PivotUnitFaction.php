<?php

namespace App\Models\Pivots;

use App\Traits\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PivotUnitFaction extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'pivot_unit_faction';

    protected $primaryKey = ['faction_id', 'unit_id'];

    public $timestamps = false;

    protected $fillable = [
        'faction_id',
        'unit_id',
    ];
}
