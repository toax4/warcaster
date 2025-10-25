<?php

namespace App\Services\Discord\Models;

use Illuminate\Support\Facades\Config;

class DiscordEmbedMessage
{
    protected array $embedOptions = [];
    protected array $mentionedRoles = [];
    protected array $buttons = [];
    protected array $selectMenus = [];

    public function __construct($embedOptions = [])
    {
        $this->embedOptions = $embedOptions;
    }

    public static function get(string $key, array $embedOptions = [])
    {
        $templates = Config::get('discord.templates');

        if (!array_key_exists($key, $templates)) {
            throw new \Exception("Template Discord '{$key}' introuvable.");
        }

        // Fusionne les valeurs par défaut avec les remplacements
        return self::make(array_merge($templates[$key], $embedOptions));
    }

    public static function all()
    {
        return Config::get('discord.templates');
    }

    public static function make(array $data = [])
    {
        $message = new self();
        $message->embedOptions = $data["embedOptions"] ?? [];
        $message->mentionedRoles = $data["mentionedRoles"] ?? [];
        $message->buttons = $data["buttons"] ?? [];
        $message->selectMenus = $data["selectMenus"] ?? [];

        return $message;
    }

    public function toArray()
    {
        return [
            "embeds" => [$this->embedOptions],
            // "mention_roles" => $this->mentionedRoles,
            // "buttons" => $this->buttons,
            // "selectMenus" => $this->selectMenus,
        ];
    }

    public function addTitle(string $title)
    {
        $this->embedOptions['title'] = $title;
        return $this;
    }

    public function addDescription(string $description)
    {
        $this->embedOptions['description'] = $description;
        return $this;
    }

    public function addColor(string $color)
    {
        $this->embedOptions['color'] = hexdec($color);
        return $this;
    }

    public function addAuthor(string $name, string $url = null, string $icon_url = null)
    {
        $this->embedOptions['author'] = [
            'name' => $name,
            'url' => $url,
            'icon_url' => $icon_url,
        ];
        return $this;
    }

    public function addTimestamp(string $timestamp = null)
    {
        $this->embedOptions['timestamp'] = $timestamp ? $timestamp : date('c');
        return $this;
    }

    public function addImage(string $url)
    {
        $this->embedOptions['image'] = [
            'url' => $url,
        ];
        return $this;
    }

    public function addField(string $name, string $value, bool $inline = false)
    {
        $this->embedOptions['fields'][] = [
            'name' => $name,
            'value' => $value,
            'inline' => $inline,
        ];
        return $this;
    }

    public function addFooter(string $text, string $icon_url = null)
    {
        $this->embedOptions['footer'] = [
            'text' => $text,
            'icon_url' => $icon_url,
        ];
        return $this;
    }

    public function addThumbnail(string $url)
    {
        $this->embedOptions['thumbnail'] = [
            'url' => $url,
        ];
        return $this;
    }

    public function addMentionRole(int $roleId)
    {
        $this->mentionedRoles[] = $roleId;
        return $this;
    }

    public function addButton(string $label, int $style = 1, string $custom_id = null, string $url = null, bool $disabled = false)
    {
        $this->buttons[] = [
            'label' => $label,
            'style' => $style,
            'custom_id' => $custom_id,
            'url' => $url,
            'disabled' => $disabled,
        ];

        return $this;
    }

    public function addSelectMenu(string $custom_id, array $options, string $placeholder = null, int $min_values = 1, int $max_values = 1)
    {
        $this->selectMenus[] = [
            'custom_id' => $custom_id,
            'options' => $options,
            'placeholder' => $placeholder,
            'min_values' => $min_values,
            'max_values' => $max_values,
        ];

        return $this;
    }
}
