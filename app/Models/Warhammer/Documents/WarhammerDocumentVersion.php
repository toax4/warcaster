<?php

namespace App\Models\Warhammer\Documents;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarhammerDocumentVersion extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "document_id",
        "remote_file",
        "local_file",
        "checksum",
        "updated_at",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'updated_at' => 'date',
    ];
}