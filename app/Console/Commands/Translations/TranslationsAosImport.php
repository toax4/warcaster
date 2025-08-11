<?php

namespace App\Console\Commands\Translations;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TranslationsAosImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:aos:imports {classes*}';

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
        $classes = $this->argument('classes');

        if ($classes == ["all"]) {
            $classes = [
                "Ability",
                "Keyword",
                "Unit",
                "Faction",
                "Weapon",
                "WeaponAbility",
                "BattleFormation",
                "Glossary",
                "Lore",
                "Phase",
                "PhaseDetail",
            ];
        }

        $countJobs = [
        ];

        foreach ($classes as $class) {
            $classWithNamespace = "App\Models\\".$class;
            $jobClass = "App\Jobs\ImportWarhammer\AoS\\"."Import".$class."TranslationJob";

            $countJobs[basename($jobClass)] = 0;

            $filePath = Str::snake($class).'.json';
            // $result = [];

            // 1. Charger le fichier si déjà présent
            if (Storage::disk('translations')->exists($filePath)) {
                $json = Storage::disk('translations')->get($filePath);
                $result = json_decode($json, true) ?? [];
                // $this->info("Ancien fichier chargé avec " . count($result) . " slugs.");
            }

            
            $objects = json_decode($json, true);
            // dump($objects);
            foreach ($objects as $object) {
                $fields = array_keys($object);
                $find = [];
                foreach ($fields as $field) {
                    if (!empty($object[$field]) && !in_array($field, ["translations"])) {
                        $find[$field] = $object[$field];
                    }
                }

                foreach ($object['translations'] as $langId => $trad) {
                    $jobClass::dispatch(
                        $find,
                        langId: $langId,
                        data: $trad
                    );
                    $countJobs[basename($jobClass)]++;
                }
            }
        }


        foreach ($countJobs as $key => $countJob) {
            Log::info("".basename(__FILE__)."::".basename($key)." - $countJob jobs");
        }
    }
}