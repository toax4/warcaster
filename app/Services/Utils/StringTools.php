<?php

namespace App\Services\Utils;

use Illuminate\Support\Str;

class StringTools
{
    public static function slug(string $text): string
    {
        // Exemple custom
        $text = str_replace(['/'], ['_'], $text);
        return Str::slug($text, '_', 'fr');
    }

    public static function cleanHtmlText(string $html): string 
    {
        return trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
}
