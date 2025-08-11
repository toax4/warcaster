<?php

namespace App\Console\Commands\Dev;

use Illuminate\Console\Command;

class DbSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:db-setup';

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
        $this->infoBlock("Export des traductions existantes");
        $this->call("translations:export");
        $this->infoBlock("Reset et migration de la base de données");
        $this->call("migrate:refresh");
        $this->infoBlock("Import des données AoS depuis la base brute");
        $this->call("aos:imports");
        $this->infoBlock("Import des traductions enregistrées");
        $this->call("translations:import");
        $this->info("🎉 Base de données prête !");
    }

    private function infoBlock(string $title)
    {
        $this->newLine(2);
        $this->line("<fg=cyan>".str_repeat('=', strlen($title) + 10)."</>");
        $this->info("<fg=cyan>$title</>");
        $this->line("<fg=cyan>".str_repeat('=', strlen($title) + 10)."</>");
        $this->newLine();
    }
}