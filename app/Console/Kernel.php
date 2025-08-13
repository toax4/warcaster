<?php

namespace App\Console;

use App\Jobs\Rss\ScrapWarhammerShop;
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

        if (app()->environment('production')) {
            $schedule->call(function () use ($date) {
                Artisan::call('queue:work --queue=high,default,imports --max-time=55');
            })
            ->everyMinute()
            ->name('worker:1')
            ->withoutOverlapping();
        }

        $schedule->call(function () use ($date) {
            Artisan::call('queue:restart');
        })->everyFiveMinutes();

        $schedule->call(function () use ($date) {
            Log::info('CRON warhammer-news & warhammer-documents execute a ' . $date);
            Artisan::call('rss:scrap-warhammer-news-fr');
            Artisan::call('rss:scrap-warhammer-documents');
        })->hourly();

        $schedule->call(function () use ($date) {
            Log::info('CRON warhammer-news-units execute a ' . $date);
            Artisan::call('rss:scrap-warhammer-shop');
        })
        ->saturdays()
        ->between('01:00', '12:00')
        ->hourly();

        $schedule->call(function () use ($date) {
            // Log::info('CRON send telegram news execute a ' . $date);
            $article = Article::where("sended", false)->orderByRaw("RAND()")->first();

            if ($article) {
                SendTelegramArticle::dispatch(article: $article);
            }
        })->everyMinute();

        $schedule->call(function () use ($date) {
            Log::info('CRON export des traductions execute a ' . $date);
            Artisan::call("translations:aos:exports all");
        })->hourly();

        $schedule->call(function () use ($date) {
            Log::info('CRON clean folder execute a ' . $date);
            Artisan::call("app:clean-folder", ["path" => $_SERVER["DOCUMENT_ROOT"]."/../storage/app/temp", "interval" => "P1D"]);
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
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}