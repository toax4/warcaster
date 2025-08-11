<?php

namespace App\Console\Commands\ImportWarhammer\AoS;

use App\Jobs\ImportWarhammer\AoS\ImportAbilityJob;
use App\Jobs\ImportWarhammer\AoS\ImportAbilityTranslationJob;
use App\Jobs\ImportWarhammer\AoS\ImportArtefactJob;
use App\Jobs\ImportWarhammer\AoS\ImportPivotAbilityKeywordJob;
use App\Services\Utils\StringTools;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportArtefacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:aos:artefacts';

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
            ImportPivotAbilityKeywordJob::class => 0,
            ImportArtefactJob::class => 0,
        ];


        // Battle Traits Abilities
        $abilities = DB::connection('mysql_aos')
        ->select("SELECT 
                    a.*
                FROM ability a
                INNER JOIN ability_group ag ON a.abilityGroupId = ag.id
                WHERE ag.abilityGroupType = 'artefactsOfPower';
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
                    'phase_id' => $ability['phase'],
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
                INNER JOIN warscroll_ability_keyword wak ON wak.keywordId = k.id
                INNER JOIN ability a ON wak.warscrollAbilityId = a.id
                INNER JOIN ability_group ag ON a.abilityGroupId = ag.id
                WHERE ag.abilityGroupType = 'artefactsOfPower';
        ");
        foreach ($keywords as $keyword) {
            $keyword = (array) $keyword;

            ImportPivotAbilityKeywordJob::dispatch(
                ability: [
                    'slug' => StringTools::slug($keyword['ability_name']),
                    'warhammer_id' => $keyword['ability_id'],
                ],
                keyword: [
                    'slug' => StringTools::slug($keyword['keyword_name']),
                    'warhammer_id' => $keyword['keyword_id'],
                ],
            );
            $countJobs[ImportPivotAbilityKeywordJob::class]++;
        }

        // Pivot Artefacts - Abilities
        $pivots = DB::connection('mysql_aos')
        ->select("SELECT 
                    a.id as ability_id,
                    a.name as ability_name,
                    faction.id AS faction_id,
                    faction.name AS faction_name
                FROM ability a
                INNER JOIN ability_group ag ON a.abilityGroupId = ag.id
                INNER JOIN faction_keyword faction ON ag.factionId = faction.id
                WHERE ag.abilityGroupType = 'artefactsOfPower';
        ");
        foreach ($pivots as $pivot) {
            $pivot = (array) $pivot;

            ImportArtefactJob::dispatch(
                ability: [
                    'slug' => StringTools::slug($pivot['ability_name']),
                    'warhammer_id' => $pivot['ability_id'],
                ],
                faction: [
                    'slug' => StringTools::slug($pivot['faction_name']),
                    'warhammer_id' => $pivot['faction_id'],
                ],
            );
            $countJobs[ImportArtefactJob::class]++;
        }

        
        foreach ($countJobs as $key => $countJob) {
            Log::info("".basename(__FILE__)."::".basename($key)." - $countJob jobs");
        }
    }
}
