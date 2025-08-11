<?php

namespace App\Models\Rss;

use App\Casts\Json;
use App\Services\Utils\StringTools;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id',
        'title',
        'link',
        'image',
        'sended',
        'data',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => Json::class,
    ];

    public function source()
    {
        return $this->belongsTo(ArticleSource::class);
    }

    public static function extractWarhammerSummary(string $text): ?string
    {
        preg_match('/(.*?)(?=POURQUOI LIRE CE LIVRE)/is', $text, $matches);
        $text = $matches[0];

        $text = preg_replace("/(<(\/)?br(\/)?>)+/", "\n", $text);
        $text = StringTools::cleanHtmlText($text);

        return trim($text);
    }
}
