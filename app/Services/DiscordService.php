<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class DiscordService
{
    protected static function send(array $payload)
    {
        if (isset($payload["webhookUrl"]) && !empty($payload["webhookUrl"])) {
            $webhookUrl = $payload["webhookUrl"];
        } else {
            $webhookUrl = env("DISCORD_WEBHOOK_SHOP_AOS");
        }

        // dump($webhookUrl);

        return Http::post($webhookUrl, $payload);
    }

    public static function sendEmbed(array $options)
    {
        $embed = [
            'title' => $options['title'] ?? null,
            'description' => $options['description'] ?? null,
            'color' => $options['color'] ?? 0x5865F2,
            'timestamp' => now()->toIso8601String(),
        ];

        // Auteur personnalisé
        if (!empty($options['author'])) {
            $embed['author'] = [
                'name' => $options['author']['name'] ?? 'Laravel Bot',
                'icon_url' => $options['author']['icon_url'] ?? null,
            ];
        }

        // Thumbnail
        if (!empty($options['thumbnail'])) {
            $embed['thumbnail'] = [
                'url' => $options['thumbnail'],
            ];
        }

        // Footer
        if (!empty($options['footer'])) {
            $embed['footer'] = [
                'text' => $options['footer']['text'] ?? '',
                'icon_url' => $options['footer']['icon_url'] ?? null,
            ];
        }

        // Champs (facultatif)
        if (!empty($options['fields'])) {
            $embed['fields'] = $options['fields'];
        }

        return self::send([
            "webhookUrl" => $options['webhookUrl'] ?? null,
            'username' => $options['username'] ?? 'Laravel Bot',
            'avatar_url' => $options['avatar_url'] ?? 'https://laravel.com/img/logomark.min.svg',
            'embeds' => [$embed],
        ]);
    }
}
