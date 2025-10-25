<?php

namespace App\Services\Discord\Models;

use Illuminate\Support\Facades\Config;

class DiscordUser
{
    protected array $datas;

    public static function get(string $key, array $overrides = [])
    {
        $users = Config::get('discord.users');

        if (!array_key_exists($key, $users)) {
            throw new \Exception("Utilisateur Discord '{$key}' introuvable.");
        }

        // Fusionne les valeurs par défaut avec les remplacements
        return self::make(array_merge($users[$key], $overrides));
    }

    public static function all()
    {
        return Config::get('discord.users');
    }

    public static function make(array $data = [])
    {
        $user = new self();
        $user->datas = $data;

        return $user;
    }

    public function getProperty($property)
    {
        return $this->datas[$property];
    }

    public function toArray()
    {
        $datas = $this->datas;
        unset($datas["webhook_url"]);
        return $datas;
    }
}
