<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class TelegramService
{
    protected string $botToken;
    protected string $chatId;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->chatId   = config('services.telegram.chat_id');
    }

    public function sendWithImage($html, $imageUrl)
    {
        $res = Http::attach(
            'photo', file_get_contents($imageUrl), 'image.jpg'
        )->post("https://api.telegram.org/bot".$this->botToken."/sendPhoto", [
            'chat_id' => $this->chatId,
            'parse_mode' => 'HTML',
            // 'photo'   => new \CURLFile($imageUrl),
            'disable_web_page_preview' => true,
            'caption' => $html
        ]);

        return $res;
    }
}