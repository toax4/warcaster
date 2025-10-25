<?php

namespace App\Jobs\Rss;

use App\Models\Rss\Article;
use App\Services\DiscordService;
use App\Services\TelegramService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class SendDiscordArticle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Article $article;
    protected string $webhookUrl;
    /**
     * Create a new job instance.
     */
    public function __construct(Article $article, $webhookUrl = null)
    {
        $this->article = $article;
        $this->webhookUrl = $webhookUrl;
    }

    /**
     * Exécution du job.
     */
    public function handle()
    {
        $source_name = $this->article->source->name;
        if ($this->article->data["productType"] == "book") {
            $icon = "📖";
            $source_name .= " - Black Library";
        } elseif ($this->article->data["productType"] == "miniatureKit") {
            $icon = "⚔️";
            $source_name .= " - " . implode(" / ", $this->article->data["games"]);
        } elseif ($this->article->data["productType"] == "rulebookCards") {
            $icon = "🪪";
            $source_name .= " - " . implode(" / ", $this->article->data["games"]);
        } elseif ($this->article->data["productType"] == "licensedProduct") {
            $icon = "🧸";
        } elseif ($this->article->data["productType"] == "gamingAccessory") {
            $icon = "🎲";
            $source_name .= " - " . implode(" / ", $this->article->data["games"]);
        } elseif ($this->article->data["productType"] == "magazine") {
            $icon = "🗞️";
            $source_name .= " - White Dwarf";
        } else {
            $icon = "🛡️";
        }

        if ($this->article->data["productType"] == "book") {
            $description = $this->article->data["summary"];
        } else {
            if (strlen($this->article->data["summary"]) > 500) {
                $description = substr($this->article->data["summary"], 0, 500) . "...";
            } else {
                $description = $this->article->data["summary"];
            }
        }

        // dump($this->webhookUrl);

        DiscordService::sendEmbed([
            "webhookUrl" => $this->webhookUrl,
            'username' => "Nouveauté Warhammer",
            'avatar_url' => "https://images.seeklogo.com/logo-png/43/2/warhammer-logo-png_seeklogo-438364.png",

            'title' => $icon . " " . $this->article->data["title"],
            'description' => $source_name,
            'color' => 0xFBCA1B,

            'thumbnail' => $this->article->image,

            'fields' => [
                ['name' => "", 'value' => $description],
                ['name' => "", 'value' => number_format($this->article->data["price"], "2", ",", " ") . " €"],
                ['name' => "", 'value' => "[🛒 Voir dans la Boutique](" . $this->article->link . ")"],
            ],
        ]);

        return 'Embed envoyé sur Discord !';
    }
}
