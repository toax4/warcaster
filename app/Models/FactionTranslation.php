<?php

namespace App\Models;

use App\Traits\HasCompositePrimaryKey;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactionTranslation extends Model
{
    use HasCompositePrimaryKey, Translatable;

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = ['faction_id', 'lang_id'];

    protected $fillable = ['faction_id', 'lang_id', 'name', 'lore'];

    protected $translationFields = [
        'name',
        'lore',
    ];


    public function faction()
    {
        return $this->belongsTo(Faction::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }
}
