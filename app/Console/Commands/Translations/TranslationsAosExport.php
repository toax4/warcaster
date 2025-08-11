<?php

namespace App\Console\Commands\Translations;

use App\Models\Ability;
use App\Services\Utils\ArrayTools;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TranslationsAosExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:aos:exports {classes*}';

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
        
        foreach ($classes as $class) {
            $classWithNamespace = "App\Models\\".$class;
            // $classTranslationWithNamespace = "App\Models\\".$class."Translation";

            $filePath = Str::snake($class).'.json';
            $result = [];

            // 1. Charger le fichier si déjà présent
            if (Storage::disk('translations')->exists($filePath)) {
                $json = Storage::disk('translations')->get($filePath);
                $result = json_decode($json, true) ?? [];
                $this->info("Ancien fichier chargé avec " . count($result) . " slugs.");
            }

            $objects = $classWithNamespace::with('translations')->orderBy('slug', 'asc')->get();
            foreach ($objects as $object) {
                if (count($object->translations) > 1) {
                    $slug = $object->slug;
                    $warhammer_id = $object->warhammer_id;

                    $key = ArrayTools::findKey($result, function ($item) use ($slug, $warhammer_id) {
                        return $item['slug'] === $slug && $item['warhammer_id'] === $warhammer_id;
                    });

                    $translations = [];
                    if ($key !== null && isset($result[$key]['translations']) && is_array($result[$key]['translations'])) {
                        $translations = $result[$key]['translations'];
                    }
                
                    foreach ($object->translations as $translation) {
                        if ($translation->lang_id != 1) { // 1 = en-US, qu'on ignore
                            $translations[$translation->lang_id] = [];

                            foreach ($translation->getTranslatableFields() as $fields) {
                                $translations[$translation->lang_id][$fields] = $translation->$fields;
                            }
                        }
                    }
        
                    if ($key !== null) {
                        ksort($translations);
                        $result[$key]["translations"] = $translations;
                    } else {
                        $result[] = [
                            'slug' => $slug,
                            'warhammer_id' => $warhammer_id,
                            "translations" => $translations,
                        ];
                    }
                }
            }

            $json = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            Storage::disk('translations')->put($filePath, $json);

            $this->info("✅ Export terminé dans storage/app/exports/$filePath");
        }
    }
}