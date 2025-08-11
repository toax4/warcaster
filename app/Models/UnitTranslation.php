<?php

namespace App\Models;

use App\Traits\HasCompositePrimaryKey;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class UnitTranslation extends Model
{
    use HasCompositePrimaryKey, Translatable;

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = ['unit_id', 'lang_id'];

    protected $fillable = [
        'unit_id',
        'lang_id',
        'name',
        'subname',
        'lore',
    ];

    protected $translationFields = [
        'name',
        'subname',
        'lore',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }
}