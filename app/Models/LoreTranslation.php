<?php

namespace App\Models;

use App\Traits\HasCompositePrimaryKey;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoreTranslation extends Model
{
    use HasCompositePrimaryKey, Translatable;

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = ['lore_id', 'lang_id'];

    protected $fillable = [
        'lore_id',
        'lang_id',
        'name',
    ];
    
    protected $translationFields = [
        'name',
    ];


    public function lore()
    {
        return $this->belongsTo(Lore::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }
}
