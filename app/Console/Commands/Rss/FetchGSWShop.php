<?php

namespace App\Console\Commands\Rss;

use App\Jobs\Rss\SendTelegramArticle;
use App\Models\Rss\Article;
use App\Models\Rss\ArticleSource;
use App\Services\TelegramService;
use App\Services\Utils\StringTools;
use App\Services\WarhammerAlgoliaService;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class FetchGSWShop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rss:scrap-gsw-shop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $source = ArticleSource::where("slug", "green_stuff_world_nouveautes")->first();
        if ($source == null) {
            $source = new ArticleSource([
                "slug" =>  "green_stuff_world_nouveautes",
                "name" =>  "Green Stuff World - Nouveautés",
            ]);

            $source->save();
        }
        $datas = [];

        $searches = [
            [
                "link" => "https://www.greenstuffworld.com/fr/nouveaux-produits",
                "channel" => env("TELEGRAM_CHAT_ID_GSW_NOUVEAUTES"),
                "warcaster_tag" => "gsw_news",
            ],
        ];

        foreach ($searches as $search) {
            $response = (new Client())->get($search["link"]);
            $html = $response->getBody()->getContents();

            $crawler = new Crawler($html);

            $articles = $crawler->filter(".products.row article.product-miniature")->each(function (Crawler $node) {
                return [
                    'title' => $node->filter("h3.product-title")->first()->text(),
                    'link' => $node->filter("h3.product-title a")->first()->attr('href'),
                    'image' => $node->filter(".thumbnail.product-thumbnail img")->first()->attr('src'),
                    'price' => str_replace(",", ".", str_replace("\u{A0}€", "", $node->filter("span.price")->first()->text())),
                    'description' => $node->filter(".product-desc")->first()->text(),
                ];
            });

            krsort($articles);
            // dd($articles);


            foreach ($articles as $data) {
                $data["channel"] = $search["channel"];
                $data["warcaster_tag"] = $search["warcaster_tag"];

                $art = Article::firstOrCreate(
                    [
                        'link' => $data["link"],
                        'source_id' => $source->id,
                    ],
                    [
                        'title' => $data["title"],
                        'image' => $data["image"],
                        'published_at' => now(),
                        'data' => $this->buildJson($data),
                    ]
                );
            }

            $this->info("✅ " . count($articles) . " articles " . $search["warcaster_tag"] . " récupérés.");
        }
    }

    private function buildJson($data)
    {
        $json = [
            "view" => "rss.telegram.gsw_shop",
            "title" => $data["title"],
            "price" => floatval($data["price"]),
            "summary" => $data["description"],
            "shop_url" => $data["link"],
            "channel" => $data["channel"],
            "warcaster_tag" => $data["warcaster_tag"],
        ];

        return $json;
    }
}
