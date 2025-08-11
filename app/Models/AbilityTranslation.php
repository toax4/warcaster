<?php

namespace App\Models;

use App\Traits\HasCompositePrimaryKey;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbilityTranslation extends Model
{
    use HasCompositePrimaryKey, Translatable;

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = ['ability_id', 'lang_id'];

    protected $fillable = [
        'ability_id',
        'lang_id',
        'name',
        'lore',
        'declare',
        'effect',
    ];
    
    protected $translationFields = [
        'name',
        'lore',
        'declare',
        'effect',
    ];


    public function ability()
    {
        return $this->belongsTo(Ability::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }
}