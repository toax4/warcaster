<?php

namespace App\Models;

use App\Models\Language;
use App\Models\Phase;
use App\Traits\HasCompositePrimaryKey;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhaseTranslation extends Model
{
    use HasCompositePrimaryKey, Translatable;

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = ['phase_id', 'lang_id'];

    protected $fillable = [
        'phase_id',
        'lang_id',
        'name',
    ];
    
    protected $translationFields = [
        'name',
    ];

    public function ability()
    {
        return $this->belongsTo(Phase::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }
}