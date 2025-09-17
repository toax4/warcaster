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
    }
}