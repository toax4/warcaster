<?php

namespace App\Models;

use App\Traits\HasCompositePrimaryKey;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class UnitTranslation extends Model
{
    use HasCompositePrimaryKey, Translatable, Searchable;

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = ['unit_id', 'lang_id'];

    protected $fillable = [
        'unit_id',
        'lang_id',
        'name',
        'subname',
        'lore',
    ];

    protected $translationFields = [
        'name',
        'subname',
        'lore',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }

    /**
     * Get the name of the index associated with the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'units_index';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            // 'id' => (int) $this->id,
            'unit_id'  => $this->unit_id,
            'name' => $this->name,
            'subname' => $this->subname,
            // 'price' => (float) $this->price,
        ];
 
        return $array;
    }

    /**
     * Get the value used to index the model.
     *
     * @return mixed
     */
    public function getScoutKey()
    {
        return $this->name;
    }
 
    /**
     * Get the key name used to index the model.
     *
     * @return mixed
     */
    public function getScoutKeyName()
    {
        return 'name';
    }
}