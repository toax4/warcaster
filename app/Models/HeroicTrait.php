<?php

namespace App\Models;

use App\Traits\HasCompositePrimaryKey;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroicTrait extends Model
{
    use HasCompositePrimaryKey, HasTranslations;

    protected $primaryKey = ['faction_id', 'ability_id'];

    protected $fillable = [
        'ability_id',
        'faction_id',
    ];
    
    public $timestamps = false;

    public function ability()
    {
        return $this->hasOne(Ability::class, "ability_id");
    }
    public function faction()
    {
        return $this->hasOne(Faction::class, "faction_id");
    }
}