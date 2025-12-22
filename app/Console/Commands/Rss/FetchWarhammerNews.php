<?php

namespace App\Console\Commands\Rss;

use App\Jobs\Rss\SendTelegramArticle;
use App\Models\Rss\Article;
use App\Models\Rss\ArticleSource;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class FetchWarhammerNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rss:scrap-warhammer-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Récupère les dernières news Warhammer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $source = ArticleSource::where("slug", "warhammer_news")->first();

        $searchs = [
            [
                "url" => 'https://www.warhammer-community.com/api/search/news/',
                "data" => '{"sortBy":"date_desc","category":"","collections":["articles"],"game_systems":[],"index":"news","locale":"fr-fr","page":0,"perPage":12,"topics":[]}',
                "base_link" => "https://www.warhammer-community.com/fr-fr/",
                "formatter" => new \IntlDateFormatter(
                    'fr_FR',
                    \IntlDateFormatter::SHORT,
                    \IntlDateFormatter::NONE,
                    null,
                    \IntlDateFormatter::GREGORIAN,
                    'd MMM yy'
                ),
                "channel" => env("TELEGRAM_CHAT_ID_NEWS_FR"),
                "parent_div_class" => ".shared-newsGridOne",
                "warcaster_tag" => "news_fr",
            ],
            [
                "url" => 'https://www.warhammer-community.com/api/search/news/',
                "data" => '{"sortBy":"date_desc","category":"","collections":["articles"],"game_systems":[],"index":"news","locale":"en-gb","page":0,"perPage":12,"topics":[]}',
                "base_link" => "https://www.warhammer-community.com/en-gb/",
                "formatter" => new \IntlDateFormatter(
                    'en-US',
                    \IntlDateFormatter::SHORT,
                    \IntlDateFormatter::NONE,
                    null,
                    \IntlDateFormatter::GREGORIAN,
                    'd MMM yy'
                ),
                "channel" => env("TELEGRAM_CHAT_ID_NEWS_EN"),
                "parent_div_class" => "",
                "warcaster_tag" => "news_en",
            ],
        ];

        foreach ($searchs as $search) {
            $articles = Http::post(
                $search["url"],
                json_decode($search["data"])
            );
            // $response = (new Client())->get($search["url"]);
            // $html = $response->getBody()->getContents();

            // $crawler = new Crawler($html);

            // $articles = $crawler->filter($search["parent_div_class"]." " . 'li article.shared-articleCard')->each(function (Crawler $node) use ($formatter) {
            //     dump("là");
            //     return [
            //         'title' => $node->filter(".newsCard-title-sm")->first()->text(),
            //         'link' => "https://www.warhammer-community.com/".$node->filter("a")->first()->attr('href'),
            //         'image' => $node->filter("figure img")->first()->attr('src'),
            //         'published_at' => Carbon::createFromTimestamp($formatter->parse($node->filter("time")->eq(1)->text()))->format('Y-m-d H:i:s')
            //     ];
            // });

            // dd($articles->json());

            foreach ($articles->json()["news"] as $article) {
                $uri = $search["base_link"] . ltrim($article["uri"], "/");
                $json = [
                    "view" => "rss.telegram.news",
                    "source_name" => $source->name,
                    "title" => $article['title'],
                    "news_url" => $uri,
                    "channel" => $search['channel'],
                    "topics" => $article['topics'],
                    "warcaster_tag" => $search['warcaster_tag'],
                ];

                $formatter = $search["formatter"];

                $art = Article::firstOrCreate(
                    [
                        'link' => $uri,
                        'source_id' => $source->id,
                    ],
                    [
                        'title' => $article['title'],
                        'image' => "https://assets.warhammer-community.com/" . $article['image']["path"],
                        'published_at' => Carbon::createFromTimestamp($formatter->parse($article["date"]))->format('Y-m-d H:i:s'),
                        'data' => $json
                    ]
                );
            }

            $this->info("✅ " . count($articles->json()["news"]) . " articles récupérés.");
        }

        $sourceMiniOfTheMonth = ArticleSource::where("slug", "miniature_of_the_month")->first();
        if ($sourceMiniOfTheMonth == null) {
            $sourceMiniOfTheMonth = new ArticleSource([
                "slug" =>  "miniature_of_the_month",
                "name" =>  "Miniature of the month",
            ]);

            $sourceMiniOfTheMonth->save();
        }


        // Minia of the month
        $articleMiniOfTheMonth = Http::post(
            "https://www.warhammer-community.com/api/search/topics/",
            json_decode('{"locale": "en-gb","type": "articles","paginate": true,"initialPage": 1,"topic": "miniature-of-the-month","index": "topics_v2","perPage": 1,"page": 1}')
        );

        foreach ($articleMiniOfTheMonth->json()["news"] as $article) {
            $uri = "https://www.warhammer-community.com/en-gb/" . ltrim($article["uri"], "/");

            $page = Http::get(
                $uri
            );

            // dd($page);
            $html = $page->body();
            // dd($html);
            $crawler = new Crawler($html);

            $articles = $crawler->filter(".article-content.wysiwyg img")->each(function (Crawler $node, $i) use ($sourceMiniOfTheMonth, $article, $uri) {
                $imgUrl = $node->attr("src");

                if (preg_match('/-mini-[a-zA-Z0-9]+.jpg/m', $imgUrl) || preg_match('/-coin-[a-zA-Z0-9]+.jpg/m', $imgUrl)) {
                    if (preg_match('/-mini-[a-zA-Z0-9]+.jpg/m', $imgUrl)) {
                        // Mini
                        $topics = ["miniature-of-the-month"];
                        $title = "Miniature of the month";
                        $warcaster_tag = "miniature-of-the-month";
                    } elseif (preg_match('/-coin-[a-zA-Z0-9]+.jpg/m', $imgUrl)) {
                        // Coin
                        $topics = ["coin-of-the-month"];
                        $title = "Coin of the month";
                        $warcaster_tag = "coin-of-the-month";
                    }

                    $json = [
                        "view" => "rss.telegram.miniature-of-the-month",
                        "source_name" => $sourceMiniOfTheMonth->name,
                        "title" => $title,
                        "news_url" => $uri,
                        "topics" => $topics,
                        "channel" => env("TELEGRAM_CHAT_ID_NEWS_FR"),
                        "warcaster_tag" => $warcaster_tag,
                    ];

                    $formatter = new \IntlDateFormatter(
                        'en-US',
                        \IntlDateFormatter::SHORT,
                        \IntlDateFormatter::NONE,
                        null,
                        \IntlDateFormatter::GREGORIAN,
                        'd MMM yy'
                    );

                    $art = Article::firstOrCreate(
                        [
                            'link' => $imgUrl,
                            'source_id' => $sourceMiniOfTheMonth->id,
                        ],
                        [
                            'title' => $title,
                            'image' => $imgUrl,
                            'published_at' => Carbon::createFromTimestamp($formatter->parse($article["date"]))->format('Y-m-d H:i:s'),
                            'data' => $json
                        ]
                    );
                }
            });
        }
    }
}
