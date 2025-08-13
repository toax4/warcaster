<?php

namespace App\Models\Warhammer\Documents;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarhammerDocument extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "game_id",
        "category_id",
        "lang_id",
        "title",
        "slug",
        "created_at",
        "warhammer_id",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'date',
    ];
}