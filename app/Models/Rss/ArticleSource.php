<?php

namespace App\Models\Rss;

use App\Services\Utils\StringTools;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleSource extends Model
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