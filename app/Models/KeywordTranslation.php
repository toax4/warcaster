<?php

namespace App\Models;

use App\Traits\HasCompositePrimaryKey;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeywordTranslation extends Model
{
    use HasCompositePrimaryKey, Translatable;

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = ['keyword_id', 'lang_id'];

    protected $fillable = ['keyword_id', 'lang_id', 'label'];

    protected $translationFields = [
        'label',
    ];


    public function keyword()
    {
        return $this->belongsTo(Keyword::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }
}
