<?php

namespace App\Console\Commands\ImportWarhammer\AoS;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class Import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:aos';

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
        Log::info("import:aos:keywords");
        Artisan::call("import:aos:keywords");
        Log::info("import:aos:units");
        Artisan::call("import:aos:units");
        Log::info("import:aos:abilities");
        Artisan::call("import:aos:abilities");
        Log::info("import:aos:factions");
        Artisan::call("import:aos:factions");
        Log::info("import:aos:weapons");
        Artisan::call("import:aos:weapons");
        Log::info("import:aos:lores");
        Artisan::call("import:aos:lores");
        Log::info("import:aos:battle-formations");
        Artisan::call("import:aos:battle-formations");
        Log::info("import:aos:battle-traits");
        Artisan::call("import:aos:battle-traits");
        Log::info("import:aos:artefacts");
        Artisan::call("import:aos:artefacts");
        Log::info("import:aos:heroic-traits");
        Artisan::call("import:aos:heroic-traits");
        Log::info("import:aos:glossaries");
        Artisan::call("import:aos:glossaries");
    }
}
