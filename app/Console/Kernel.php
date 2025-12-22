<?php

namespace App\Console;

use App\Jobs\Rss\ScrapWarhammerShop;
use App\Jobs\Rss\SendDiscordArticle;
use App\Jobs\Rss\SendTelegramArticle;
use App\Models\Rss\Article;
use DateTime;
use DateTimeZone;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $date = (new DateTime('now', new DateTimeZone("Europe/Paris")))->format("H:i:s d-m-Y");

        if (config('app.env') == 'production') {
            $schedule->call(function () use ($date) {
                // Log::channel('cron')->info('CRON queue:work execute a ' . $date);
                Artisan::call('queue:work --queue=high,default,imports --max-time=55');
            })
                ->everyMinute()
                ->name('worker:1');
            // ->withoutOverlapping();
        }

        $schedule->call(function () use ($date) {
            Artisan::call('queue:restart');
        })->everyFiveMinutes();

        $schedule->call(function () use ($date) {
            Log::channel('cron')->info('CRON warhammer-news execute a ' . $date);
            Artisan::call('rss:scrap-warhammer-news');
            Artisan::call('rss:scrap-gsw-shop');
        })->hourly();

        $schedule->call(function () use ($date) {
            Log::channel('cron')->info('CRON warhammer-documents execute a ' . $date);
            Artisan::call('rss:scrap-warhammer-documents');

            Artisan::call("app:clean-folder", ["path" => base_path("/storage/app/temp/fr_FR"), "interval" => "PT1S", "--cleanFolder"]);
            Artisan::call("app:clean-folder", ["path" => base_path("/storage/app/temp/en_US"), "interval" => "PT1S", "--cleanFolder"]);
        })->dailyAt("12:00");


        $schedule->call(function () use ($date) {
            Log::channel('cron')->info('CRON warhammer-news-units execute a ' . $date);
            Artisan::call('rss:scrap-warhammer-shop');
        })
            ->saturdays()
            ->between('01:00', '12:00')
            ->hourly();

        $schedule->call(function () use ($date) {
            // Log::channel('cron')->info('CRON send telegram news execute a ' . $date);
            // $article = Article::where("sended", 0)->orderBy("published_at", "asc")->orderby("id", "asc")->first();
            $articles = Article::where("sended", 0)->orderBy("published_at", "asc")->orderby("id", "asc")->limit(3)->get();

            foreach ($articles as $article) {
                if ($article) {
                    SendTelegramArticle::dispatch(article: $article);

                    if ($article->data["warcaster_tag"] == "shop_other") {
                        SendDiscordArticle::dispatch(article: $article, webhookUrl: env("DISCORD_WEBHOOK_SHOP_OTHER"));
                    }
                    if ($article->data["warcaster_tag"] == "shop_aos") {
                        SendDiscordArticle::dispatch(article: $article, webhookUrl: env("DISCORD_WEBHOOK_SHOP_AOS"));
                    }
                    if ($article->data["warcaster_tag"] == "miniature-of-the-month" || $article->data["warcaster_tag"] == "coin-of-the-month") {
                        SendDiscordArticle::dispatch(article: $article, webhookUrl: env("DISCORD_WEBHOOK_NEWS_MINIA_OF_THE_MONTH"));
                    }
                }
            }
        })->everyMinute();

        $schedule->call(function () use ($date) {
            Log::channel('cron')->info('CRON export des traductions execute a ' . $date);
            Artisan::call("translations:aos:exports all");
        })->hourly();

        $schedule->call(function () use ($date) {
            Log::channel('cron')->info('CRON clean folder execute a ' . $date);
            Artisan::call("app:clean-folder", ["path" => base_path("/storage/app/temp"), "interval" => "P1D"]);
        })->hourly();
    }

    /**
     * Get the timezone that should be used by default for scheduled events.
     */
    protected function scheduleTimezone(): DateTimeZone|string|null
    {
        return 'Europe/Paris';
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
