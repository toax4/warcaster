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

class FetchWarhammerShop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rss:scrap-warhammer-shop';

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
        $source = ArticleSource::where("slug", "warhammer_shop")->first();
        $datas = [];

        $searches = [
            [
                "data" => [
                    [
                        'isNewRelease:true',
                        'isPreOrder:true',
                    ],
                    [
                        "GameSystemsRoot.lvl0:Warhammer 40,000",
                    ],
                ],
                "channel" => env("TELEGRAM_CHAT_ID_SHOP_40K_FR"),
                "warcaster_tag" => "shop_40k",
            ],
            [
                "data" => [
                    [
                        'isNewRelease:true',
                        'isPreOrder:true',
                    ],
                    [
                        "GameSystemsRoot.lvl0:Age of Sigmar",
                    ],
                ],
                "channel" => env("TELEGRAM_CHAT_ID_SHOP_AOS_FR"),
                "warcaster_tag" => "shop_aos",
            ],
            [
                "data" => [
                    [
                        'isNewRelease:true',
                        'isPreOrder:true',
                    ],
                    [
                        "GameSystemsRoot.lvl0:The Horus Heresy",
                    ],
                ],
                "channel" => env("TELEGRAM_CHAT_ID_SHOP_HORUS_FR"),
                "warcaster_tag" => "shop_horus_heresy",
            ],
            [
                "data" => [
                    [
                        'isNewRelease:true',
                        'isPreOrder:true',
                    ],
                    [
                        "GameSystemsRoot.lvl0:Le Vieux Monde",
                    ],
                ],
                "channel" => env("TELEGRAM_CHAT_ID_SHOP_OLD_WORLD_FR"),
                "warcaster_tag" => "shop_old_world",
            ],
        ];

        foreach ($searches as $search) {
            $response = WarhammerAlgoliaService::fetch($search["data"]);

            foreach (($response->json())["results"][0]["hits"] as $data) {
                // dd($data);
                if (preg_match('/\(anglais\)/mi', $data["name"])) {
                    // Skip si c'est en anglais
                    continue;
                }

                $data["channel"] = $search["channel"];
                $data["warcaster_tag"] = $search["warcaster_tag"];

                $art = Article::firstOrCreate(
                    [
                        'link' => $this->buildShopLink($data),
                        'source_id' => $source->id,
                    ],
                    [
                        'title' => $data["name"],
                        'image' => "https://www.warhammer.com" . $data["images"][0],
                        'published_at' => now(),
                        'data' => $this->buildJson($data),
                    ]
                );
            }

            $this->info("✅ " . count(($response->json())["results"][0]["hits"]) . " articles " . $search["warcaster_tag"] . " récupérés.");
        }

        // array_push($datas, ...($response->json())["results"][0]["hits"]);

        $response = WarhammerAlgoliaService::fetch([
            [
                'isNewRelease:true',
                'isPreOrder:true',
            ],
            [
                "productType:licensedProduct",
                "productType:magazine",
                "productType:book",
            ]
        ]);
        // dd(($response->json())["results"][0]["hits"]);
        // array_push($datas, ...($response->json())["results"][0]["hits"]);

        // dd($datas);
        foreach (($response->json())["results"][0]["hits"] as $data) {
            // dd($data);
            if (preg_match('/\(anglais\)/mi', $data["name"])) {
                // Skip si c'est en anglais
                continue;
            }

            $data["channel"] = env("TELEGRAM_CHAT_ID_SHOP_OTHER_FR");
            $data["warcaster_tag"] = "shop_other";

            $art = Article::firstOrCreate(
                [
                    'link' => $this->buildShopLink($data),
                    'source_id' => $source->id,
                ],
                [
                    'title' => $data["name"],
                    'image' => "https://www.warhammer.com" . $data["images"][0],
                    'published_at' => now(),
                    'data' => $this->buildJson($data),
                ]
            );
        }

        $this->info("✅ " . count(($response->json())["results"][0]["hits"]) . " articles " . "shop_other" . " récupérés.");
    }

    private function buildJson($data)
    {
        $json = [
            "view" => "rss.telegram.shop",
            "title" => $data["name"],
            "price" => ($data["ctPrice"]["centAmount"] / 100),
            "summary" => StringTools::cleanHtmlText(preg_split('/(<(\/)?br(\/)?>)+/', $data["description"])[0]),
"shop_url" => "https://www.warhammer.com/fr-FR/shop/" . $data["slug"],
"serie" => $data["series"] ?? null,
"productType" => $data["productType"],
"games" => $data["GameSystemsRoot"]["lvl0"] ?? null,
"isNewRelease" => $data['isNewRelease'],
"isPreOrder" => $data['isPreOrder'],
"channel" => $data["channel"],
"warcaster_tag" => $data["warcaster_tag"],
];

if (in_array($data["productType"], ["book"])) {
$json["summary"] = Article::extractWarhammerSummary($data["description"]);
}

return $json;
}

private function buildShopLink($data)
{
return "https://www.warhammer.com/fr-FR/shop/" . $data["slug"];
}
}