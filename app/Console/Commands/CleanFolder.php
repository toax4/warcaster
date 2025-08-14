<?php

namespace App\Console\Commands;

use DateInterval;
use DateTime;
use DirectoryIterator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanFolder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-folder {path} {interval=P1W} {--deleteFolder}';

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
        Log::info("Clean Folder Path : " . $this->argument("path"));

        $date = new DateTime();
        $date->sub(new DateInterval($this->argument("interval")));

        $this->cleanFolder($this->argument("path"), $date);
    }

    private function cleanFolder(string $path, DateTime $date)
    {
        // dd($path, file_exists($path));
        if (!file_exists($path)) {
            Log::error("Clean Folder Path Not Found : " . $this->argument("path"));
            return;
        }

        $d = new DirectoryIterator($path);
        foreach ($d as $f) {
            # Si le nom du dossier vaut . ou .. on passe au suivant
            if ($f->isDot()) {
                continue;
            }
        
            # On ne change rien sur les dossiers
            if ($f->isDir()) {
                $this->cleanFolder($f->getPathname(), $date);
                if ($this->option('deleteFolder')) {
                    rmdir($f->getPathname());
                }
                continue;
            }
        
            $d = \DateTime::createFromFormat("U", $f->getMTime());
            if ($d <= $date) {
                unlink($f->getPathname());
            }
        }
    }
}