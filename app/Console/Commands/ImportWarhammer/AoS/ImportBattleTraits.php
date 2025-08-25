<?php

namespace App\Console\Commands\ImportWarhammer\AoS;

use App\Jobs\ImportWarhammer\AoS\ImportAbilityJob;
use App\Jobs\ImportWarhammer\AoS\ImportAbilityTranslationJob;
use App\Jobs\ImportWarhammer\AoS\ImportBattleTraitJob;
use App\Jobs\ImportWarhammer\AoS\ImportKeywordJob;
use App\Jobs\ImportWarhammer\AoS\ImportKeywordTranslationJob;
use App\Jobs\ImportWarhammer\AoS\ImportPivotAbilityKeywordJob;
use App\Services\Utils\StringTools;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportBattleTraits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:aos:battle-traits';

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
            ImportKeywordJob::class => 0,
            ImportKeywordTranslationJob::class => 0,
            ImportPivotAbilityKeywordJob::class => 0,
            ImportBattleTraitJob::class => 0,
        ];


        // Battle Traits Abilities
        $abilities = DB::connection('mysql_aos')
        ->select("SELECT 
                    a.*
                FROM ability a
                INNER JOIN ability_group ag ON a.abilityGroupId = ag.id
                WHERE ag.abilityGroupType = 'battleTraits';
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
        
        // Keywords
        $keywords = DB::connection('mysql_aos')
        ->select("SELECT 
                    k.id as keyword_id,
                    k.name as keyword_name,
                    a.id AS ability_id,
                    a.name AS ability_name
                FROM keyword k
                INNER JOIN ability_keyword wak ON wak.keywordId = k.id
                INNER JOIN ability a ON wak.abilityId = a.id
                INNER JOIN ability_group ag ON a.abilityGroupId = ag.id
                WHERE ag.abilityGroupType = 'battleTraits';
        ");
        foreach ($keywords as $keyword) {
            $keyword = (array) $keyword;

            $findKeyword = [
                'slug' => StringTools::slug($keyword['keyword_name']),
                'warhammer_id' => $keyword['keyword_id'],
            ];

            ImportKeywordJob::dispatch(
                find: $findKeyword,
                data: []
            );
            $countJobs[ImportKeywordJob::class]++;

            ImportKeywordTranslationJob::dispatch(
                keyword: $findKeyword,
                langId: 1,
                data: [
                    'label' => $keyword['keyword_name'],
                ],
            );
            $countJobs[ImportKeywordTranslationJob::class]++;


            ImportPivotAbilityKeywordJob::dispatch(
                ability: [
                    'slug' => StringTools::slug($keyword['ability_name']),
                    'warhammer_id' => $keyword['ability_id'],
                ],
                keyword: $findKeyword,
            );
            $countJobs[ImportPivotAbilityKeywordJob::class]++;
        }

        // Pivot Battle Trait - Abilities
        $pivots = DB::connection('mysql_aos')
        ->select("SELECT 
                    a.id as ability_id,
                    a.name as ability_name,
                    faction.id AS faction_id,
                    faction.name AS faction_name
                FROM ability a
                INNER JOIN ability_group ag ON a.abilityGroupId = ag.id
                INNER JOIN faction_keyword faction ON ag.factionId = faction.id
                WHERE ag.abilityGroupType = 'battleTraits';
        ");
        foreach ($pivots as $pivot) {
            $pivot = (array) $pivot;

            ImportBattleTraitJob::dispatch(
                ability: [
                    'slug' => StringTools::slug($pivot['ability_name']),
                    'warhammer_id' => $pivot['ability_id'],
                ],
                faction: [
                    'slug' => StringTools::slug($pivot['faction_name']),
                    'warhammer_id' => $pivot['faction_id'],
                ],
            );
            $countJobs[ImportBattleTraitJob::class]++;
        }

        
        foreach ($countJobs as $key => $countJob) {
            Log::info("".basename(__FILE__)."::".basename($key)." - $countJob jobs");
        }
    }
}
