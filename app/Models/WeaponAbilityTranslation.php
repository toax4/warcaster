<?php

namespace App\Models;

use App\Traits\HasCompositePrimaryKey;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeaponAbilityTranslation extends Model
{
    use HasCompositePrimaryKey, Translatable;

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = ['weapon_ability_id', 'lang_id'];

    protected $fillable = [
        'weapon_ability_id',
        'lang_id',
        'name',
        'lore',
        'rules'
    ];
    
    protected $translationFields = [
        'name',
        'lore',
        'rules'
    ];


    public function weaponAbility()
    {
        return $this->belongsTo(WeaponAbility::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }
}
