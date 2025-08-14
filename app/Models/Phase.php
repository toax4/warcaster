<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'slug',
        'displayOrder',
        'hexcolor',
    ];

    public $timestamps = false;

    public function translations()
    {
        return $this->hasMany(PhaseTranslation::class)->orderBy("lang_id", "asc");
    }
}