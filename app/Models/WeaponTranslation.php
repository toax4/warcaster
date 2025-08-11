<?php

namespace App\Models;

use App\Traits\HasCompositePrimaryKey;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeaponTranslation extends Model
{
    use HasCompositePrimaryKey, Translatable;

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = ['weapon_id', 'lang_id'];

    protected $fillable = [
        'weapon_id',
        'lang_id',
        'name',
    ];

    protected $translationFields = [
        'name',
    ];


    public function weapon()
    {
        return $this->belongsTo(Weapon::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }
}
