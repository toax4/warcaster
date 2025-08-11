<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lore extends Model
{
    use HasFactory, HasTranslations;

    // protected $table = 'abilities';

    protected $fillable = [
        'slug',
        'points',
        'warhammer_id',
    ];
    
    public $timestamps = false;

    public function translations()
    {
        return $this->hasMany(LoreTranslation::class)->orderBy("lang_id", "asc");
    }
}