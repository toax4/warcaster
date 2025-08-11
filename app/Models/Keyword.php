<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = ['slug', 'warhammer_id'];
    public $timestamps = false;

    public function translations()
    {
        return $this->hasMany(KeywordTranslation::class)->orderBy("lang_id", "asc");
    }
}
