<?php

namespace App\Console\Commands\ImportWarhammer\AoS;

use App\Jobs\ImportWarhammer\AoS\ImportAbilityJob;
use App\Jobs\ImportWarhammer\AoS\ImportAbilityTranslationJob;
use App\Jobs\ImportWarhammer\AoS\ImportLoreJob;
use App\Jobs\ImportWarhammer\AoS\ImportLoreTranslationJob;
use App\Jobs\ImportWarhammer\AoS\ImportPivotAbilityKeywordJob;
use App\Jobs\ImportWarhammer\AoS\ImportPivotFactionLoreJob;
use App\Jobs\ImportWarhammer\AoS\ImportPivotLoreAbilityJob;
use App\Services\Utils\StringTools;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportLores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:aos:lores';

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
        $countJobs = [
            ImportAbilityJob::class => 0,
            ImportAbilityTranslationJob::class => 0,
            ImportLoreJob::class => 0,
            ImportLoreTranslationJob::class => 0,
            ImportPivotAbilityKeywordJob::class => 0,
            ImportPivotFactionLoreJob::class => 0,
            ImportPivotLoreAbilityJob::class => 0,
        ];

        // Abilities
        $abilities = DB::connection('mysql_aos')
        ->select("SELECT 
                    la.*
                FROM lore_ability la;
        ");
        foreach ($abilities as $ability) {
            $ability = (array) $ability;

            $findAbility = [
                'slug' => StringTools::slug($ability['name']),
                'warhammer_id' => $ability['id'],
            ];

            ImportAbilityJob::dispatch(
                find: $findAbility,
                data: [
                    'phase' => $ability['phase'],
                    'phase_detail' => $ability["phaseDetails"],
                    'cp_cost' => $ability['cpCost'] ?? null,
                    'points' => $ability['points'] ?? null,
                ]
            );
            $countJobs[ImportAbilityJob::class]++;

            ImportAbilityTranslationJob::dispatch(
                ability: $findAbility,
                langId: 1,
                data: [
                    'name' => $ability['name'],
                    "lore" => $ability["lore"],
                    "declare" => $ability["declare"],
                    "effect" => $ability["effect"],
                ]
            );
            $countJobs[ImportAbilityTranslationJob::class]++;
        }

        // Lores
        $lores = DB::connection('mysql_aos')
        ->select("SELECT 
                    l.*
                FROM lore l;
        ");
        foreach ($lores as $lore) {
            $lore = (array) $lore;

            $findLore = [
                'slug' => StringTools::slug($lore['name']),
                'warhammer_id' => $lore['id'],
            ];

            ImportLoreJob::dispatch(
                find: $findLore,
                data: []
            );
            $countJobs[ImportLoreJob::class]++;

            ImportLoreTranslationJob::dispatch(
                lore: $findLore,
                langId: 1,
                data: [
                    "name" => $lore["name"]
                ]
            );
            $countJobs[ImportLoreTranslationJob::class]++;
        }

        // Pivot Lores -> Abilities
        $lores = DB::connection('mysql_aos')
        ->select("SELECT 
                    l.id AS lore_id, 
                    l.name AS lore_name, 
                    la.id AS ability_id, 
                    la.name AS ability_name
                FROM lore l 
                INNER JOIN lore_ability la ON la.loreId = l.id;
        ");
        foreach ($lores as $lore) {
            $lore = (array) $lore;

            ImportPivotLoreAbilityJob::dispatch(
                lore: [
                    'slug' => StringTools::slug($lore['lore_name']),
                    'warhammer_id' => $lore['lore_id'],
                ],
                ability: [
                    'slug' => StringTools::slug($lore['ability_name']),
                    'warhammer_id' => $lore['ability_id'],
                ],
            );
            $countJobs[ImportPivotLoreAbilityJob::class]++;
        }

        // Pivot Abilities -> Keys
        $lores = DB::connection('mysql_aos')
        ->select("SELECT 
                    k.id AS keyword_id, 
                    k.name AS keyword_name, 
                    la.id AS ability_id, 
                    la.name AS ability_name
                FROM lore_ability la 
                INNER JOIN lore_ability_keyword lak ON la.id = lak.loreAbilityId
                INNER JOIN keyword k ON k.id = lak.keywordId;
        ");
        foreach ($lores as $lore) {
            $lore = (array) $lore;

            ImportPivotAbilityKeywordJob::dispatch(
                keyword: [
                    'slug' => StringTools::slug($lore['keyword_name']),
                    'warhammer_id' => $lore['keyword_id'],
                ],
                ability: [
                    'slug' => StringTools::slug($lore['ability_name']),
                    'warhammer_id' => $lore['ability_id'],
                ],
            );
            $countJobs[ImportPivotAbilityKeywordJob::class]++;
        }

        // Pivot Lore -> Faction
        $lores = DB::connection('mysql_aos')
        ->select("SELECT 
                    fk.id AS faction_id, 
                    fk.name AS faction_name, 
                    l.id AS lore_id, 
                    l.name AS lore_name
                FROM lore l
                INNER JOIN faction_keyword fk ON fk.id = l.factionId;
        ");
        foreach ($lores as $lore) {
            $lore = (array) $lore;

            ImportPivotFactionLoreJob::dispatch(
                lore: [
                    'slug' => StringTools::slug($lore['lore_name']),
                    'warhammer_id' => $lore['lore_id'],
                ],
                faction: [
                    'slug' => StringTools::slug($lore['faction_name']),
                    'warhammer_id' => $lore['faction_id'],
                ],
            );
            $countJobs[ImportPivotFactionLoreJob::class]++;
        }

        
        foreach ($countJobs as $key => $countJob) {
            Log::info("".basename(__FILE__)."::".basename($key)." - $countJob jobs");
        }
    }
}
