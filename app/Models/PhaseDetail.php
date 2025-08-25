<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhaseDetail extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'slug',
    ];

    public $timestamps = false;

    public function translations()
    {
        return $this->hasMany(PhaseDetailTranslation::class)->orderBy("lang_id", "asc");
    }
}