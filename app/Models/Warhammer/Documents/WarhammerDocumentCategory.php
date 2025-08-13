<?php

namespace App\Models\Warhammer\Documents;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarhammerDocumentCategory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "title",
        "slug",
    ];
}