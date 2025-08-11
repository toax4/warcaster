<?php

namespace App\Models;

use App\Traits\HasCompositePrimaryKey;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BattleFormationTranslation extends Model
{
    use HasCompositePrimaryKey, Translatable;

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = ['battle_formation_id', 'lang_id'];

    protected $fillable = [
        'battle_formation_id',
        'lang_id',
        'name',
    ];
    
    protected $translationFields = [
        'name',
    ];


    public function battleFormation()
    {
        return $this->belongsTo(BattleFormation::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }
}
