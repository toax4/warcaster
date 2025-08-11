<?php

namespace App\Models;

use App\Traits\HasCompositePrimaryKey;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlossaryTranslation extends Model
{
    use HasCompositePrimaryKey, Translatable;

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = ['glossary_id', 'lang_id'];

    protected $fillable = [
        'glossary_id',
        'lang_id',
        'name',
        'content',
    ];
    
    protected $translationFields = [
        'name',
        'content',
    ];


    public function glossary()
    {
        return $this->belongsTo(Ability::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }
}