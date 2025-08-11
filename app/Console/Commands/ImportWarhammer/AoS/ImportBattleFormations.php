<?php

namespace App\Console\Commands\ImportWarhammer\AoS;

use App\Jobs\ImportWarhammer\AoS\ImportAbilityJob;
use App\Jobs\ImportWarhammer\AoS\ImportAbilityTranslationJob;
use App\Jobs\ImportWarhammer\AoS\ImportBattleFormationJob;
use App\Jobs\ImportWarhammer\AoS\ImportBattleFormationTranslationJob;
use App\Jobs\ImportWarhammer\AoS\ImportPivotAbilityKeywordJob;
use App\Jobs\ImportWarhammer\AoS\ImportPivotBattleFormationAbilityJob;
use App\Services\Utils\StringTools;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportBattleFormations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:aos:battle-formations';

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
            ImportBattleFormationJob::class => 0,
            ImportBattleFormationTranslationJob::class => 0,
            ImportAbilityJob::class => 0,
            ImportAbilityTranslationJob::class => 0,
            ImportPivotAbilityKeywordJob::class => 0,
            ImportPivotBattleFormationAbilityJob::class => 0,
        ];

        
        // Battle formations
        $battleFormations = DB::connection('mysql_aos')
        ->select("SELECT 
                    bf.*
                FROM battle_formation bf;
        ");
        foreach ($battleFormations as $battleFormation) {
            $battleFormation = (array) $battleFormation;

            $findBattleFormation = [
                'slug' => StringTools::slug($battleFormation['name']),
                'warhammer_id' => $battleFormation['id'],
            ];

            ImportBattleFormationJob::dispatch(
                find: $findBattleFormation,
                data: [
                    'points' => $battleFormation['points'] ?? null,
                ]
            );
            $countJobs[ImportBattleFormationJob::class]++;

            ImportBattleFormationTranslationJob::dispatch(
                battleFormation: $findBattleFormation,
                langId: 1,
                data: [
                    'name' => $battleFormation['name'],
                ]
            );
            $countJobs[ImportBattleFormationTranslationJob::class]++;
        }

        // Battle formation Abilities
        $battleFormationAbilities = DB::connection('mysql_aos')
        ->select("SELECT 
                    bfr.*
                FROM battle_formation_rule bfr;
        ");
        foreach ($battleFormationAbilities as $battleFormationAbility) {
            $battleFormationAbility = (array) $battleFormationAbility;

            $findAbility = [
                'slug' => StringTools::slug($battleFormationAbility['name']),
                'warhammer_id' => $battleFormationAbility['id'],
            ];

            ImportAbilityJob::dispatch(
                find: $findAbility,
                data: [
                    'phase_id' => $battleFormationAbility['phase'],
                    'cp_cost' => $battleFormationAbility['cpCost'] ?? null,
                    // 'points' => $ability['points'] ?? null,
                ]
            );
            $countJobs[ImportAbilityJob::class]++;

            ImportAbilityTranslationJob::dispatch(
                ability: $findAbility,
                langId: 1,
                data: [
                 'name' => $battleFormationAbility['name'],
                 "lore" => $battleFormationAbility["lore"],
                 "declare" => $battleFormationAbility["declare"],
                 "effect" => $battleFormationAbility["effect"],
                ]
            );
            $countJobs[ImportAbilityTranslationJob::class]++;
        }

        // Keywords
        $keywords = DB::connection('mysql_aos')
        ->select("SELECT 
                    bfr.id as ability_id,
                    bfr.name as ability_name,
                    k.id as k_id,
                    k.name as k_name
                FROM battle_formation_rule bfr
                INNER JOIN battle_formation_rule_keyword bfrk ON bfrk.battleFormationRuleId = bfr.id
                INNER JOIN keyword k ON bfrk.keywordId = k.id;
        ");
        foreach ($keywords as $keyword) {
            $keyword = (array) $keyword;

            ImportPivotAbilityKeywordJob::dispatch(
                ability: [
                    'slug' => StringTools::slug($keyword['ability_name']),
                    'warhammer_id' => $keyword['ability_id'],
                ],
                keyword: [
                    'slug' => StringTools::slug($keyword['k_name']),
                    'warhammer_id' => $keyword['k_id'],
                ],
            );
            $countJobs[ImportPivotAbilityKeywordJob::class]++;
        }

        // Pivot Battle formations -> Abilities
        $pivots = DB::connection('mysql_aos')
        ->select("SELECT 
                    bfr.id as ability_id,
                    bfr.name as ability_name,
                    bf.id as bf_id,
                    bf.name as bf_name
                FROM battle_formation bf
                INNER JOIN battle_formation_rule bfr ON bf.id = bfr.battleFormationId;
        ");
        foreach ($pivots as $pivot) {
            $pivot = (array) $pivot;

            ImportPivotBattleFormationAbilityJob::dispatch(
                ability: [
                    'slug' => StringTools::slug($pivot['ability_name']),
                    'warhammer_id' => $pivot['ability_id'],
                ],
                battleFormation : [
                    'slug' => StringTools::slug($pivot['bf_name']),
                    'warhammer_id' => $pivot['bf_id'],
                ],
            );
            $countJobs[ImportPivotBattleFormationAbilityJob::class]++;
        }


        foreach ($countJobs as $key => $countJob) {
            Log::info("".basename(__FILE__)."::".basename($key)." - $countJob jobs");
        }
    }
}
