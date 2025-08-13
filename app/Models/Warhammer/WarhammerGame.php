<?php

namespace App\Models\Warhammer;

use App\Services\Utils\StringTools;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarhammerGame extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        "name",
    ];


    public function setNameAttribute($value)
    {
        $this->attributes['slug'] = StringTools::slug($value);
        $this->attributes['name'] = $value;
    }
}