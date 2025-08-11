<?php

namespace App\Models\Pivots;

use App\Traits\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PivotUnitKeyword extends Model
{
    use HasCompositePrimaryKey;

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = ['unit_id', 'keyword_id'];

    protected $table = 'pivot_unit_keyword';

    protected $fillable = [
        'unit_id',
        'keyword_id',
    ];
}