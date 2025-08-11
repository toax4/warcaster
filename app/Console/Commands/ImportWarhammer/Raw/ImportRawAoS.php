<?php

namespace App\Console\Commands\ImportWarhammer\Raw;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImportRawAoS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:raw:aos';

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
        $file = Storage::disk("temp")->get("dump_aos.json");
        if (!$file) {
            Log::error("Pas de fichier dump_aos.json");
            return;
        }
        $json = json_decode($file, true);
        // dump($json);

        $database = DB::connection('mysql_aos');

        foreach ($json["data"] as $sectionKey => $sectionValue) {
            // dump($sectionKey, $sectionValue);
            // dump($sectionValue[0]);

            if(empty($sectionValue) || !isset($sectionValue[0])) {
                // dump($sectionKey);
                continue;
            }

            $colonnes = [];
            foreach ($sectionValue[0] as $key => $value) {
                if (is_int($value)) {
                    $colonnes[] = "`$key` INT";
                } elseif (is_float($value)) {
                    $colonnes[] = "`$key` FLOAT";
                } else {
                    $colonnes[] = "`$key` TEXT";
                }
            }

            $database->statement("DROP TABLE IF EXISTS `$sectionKey`");
            $database->statement("CREATE TABLE IF NOT EXISTS `$sectionKey` (".implode(", ", $colonnes).")");

            $this->line('<fg=cyan>'.$sectionKey.'</>');

            $bar2 = $this->output->createProgressBar(count($json["data"]));
            $bar2->start();
            foreach ($sectionValue as $keyElement => $valueElement) {
                // dd($keyElement, $valueElement);

                $colonnesInsert = [];
                $valuesInsert = [];
                foreach ($valueElement as $key => $value) {
                    // dd($key, $value);

                    if ($value == null) {
                        continue;
                    }

                    $colonnesInsert[] = "`$key`";
                    if (is_int($value) || is_float($value)) {
                        $valuesInsert[] = $value;
                    } else {
                        $valuesInsert[] = $database->escape($value);
                        
                    }
                }
                
                $bar2->advance();
    
                // dd($colonnesInsert, $valuesInsert, "INSERT INTO $sectionKey (".implode(", ", $colonnesInsert).") VALUES (".implode(", ", $valuesInsert).")");
    
                $database->insert("INSERT INTO `$sectionKey` (".implode(", ", $colonnesInsert).") VALUES (".implode(", ", $valuesInsert).")");
            }
            $bar2->finish();
            $this->newLine();
        }
    }
}
