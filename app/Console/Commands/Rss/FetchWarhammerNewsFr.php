<?php

namespace App\Console\Commands\Rss;

use App\Jobs\Rss\SendTelegramArticle;
use App\Models\Rss\Article;
use App\Models\Rss\ArticleSource;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class FetchWarhammerNewsFr extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rss:scrap-warhammer-news-fr';

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
        $source = ArticleSource::where("slug", "warhammer_news_fr")->first();
        $url = 'https://www.warhammer-community.com/fr-fr/';

        $formatter = new \IntlDateFormatter(
            'fr_FR',
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::NONE,
            null,
            \IntlDateFormatter::GREGORIAN,
            'd MMM yy'
        );

        $response = (new Client())->get($url);
        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);

        $articles = $crawler->filter('article.shared-articleCard')->each(function (Crawler $node) use ($formatter) {
            return [
                'title' => $node->filter(".newsCard-title-sm")->first()->text(),
                'link' => "https://www.warhammer-community.com/".$node->filter("a")->first()->attr('href'),
                'image' => $node->filter("figure img")->first()->attr('src'),
                'published_at' => Carbon::createFromTimestamp($formatter->parse($node->filter("time")->eq(1)->text()))->format('Y-m-d H:i:s')
            ];
        });

        foreach ($articles as $article) {
            $json = [
                "view" => "rss.telegram.news",
                "source_name"=> $source->name,
                "title"=> $article['title'],
                "news_url"=> $article['link'],
            ];

            $art = Article::firstOrCreate(
                [
                    'link' => $article['link'],
                    'source_id' => $source->id,
                ],
                [
                    'title' => $article['title'],
                    'image' => $article['image'],
                    'published_at' => $article['published_at'], // ou extraire depuis la page si dispo
                    'data' => $json
                ]
            );
        }

        $this->info("✅ " . count($articles) . " articles récupérés.");
    }
}
