<?php

namespace App\Models;

use App\Traits\HasCompositePrimaryKey;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhaseDetailTranslation extends Model
{
    use HasCompositePrimaryKey, Translatable;

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = ['phase_detail_id', 'lang_id'];

    protected $fillable = [
        "phase_detail_id",
        "lang_id",
        "name",
    ];

     protected $translationFields = [
        'name',
    ];

    public function phaseDetail()
    {
        return $this->belongsTo(PhaseDetail::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }

}
