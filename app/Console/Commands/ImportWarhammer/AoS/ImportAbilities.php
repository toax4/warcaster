<?php

namespace App\Console\Commands\ImportWarhammer\AoS;

use App\Jobs\ImportWarhammer\AoS\ImportAbilityJob;
use App\Jobs\ImportWarhammer\AoS\ImportAbilityTranslationJob;
use App\Jobs\ImportWarhammer\AoS\ImportPhaseDetailJob;
use App\Jobs\ImportWarhammer\AoS\ImportPhaseDetailTranslationJob;
use App\Jobs\ImportWarhammer\AoS\ImportPivotAbilityKeywordJob;
use App\Jobs\ImportWarhammer\AoS\ImportPivotUnitAbilityJob;
use App\Models\Phase;
use App\Services\Utils\StringTools;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportAbilities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:aos:abilities';

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
            ImportPhaseDetailJob::class => 0,
            ImportPhaseDetailTranslationJob::class => 0,
            ImportAbilityJob::class => 0,
            ImportAbilityTranslationJob::class => 0,
            ImportPivotAbilityKeywordJob::class => 0,
            ImportPivotUnitAbilityJob::class => 0,
        ];

        $abilities = DB::connection('mysql_aos')
        ->select("SELECT 
                    wa.*
                FROM warscroll_ability wa;
        ");
        foreach ($abilities as $ability) {
            $ability = (array) $ability;

            $findPhaseDetail = [
                'slug' => StringTools::slug($ability['phaseDetails']),
            ];

            ImportPhaseDetailJob::dispatch(
                find: $findPhaseDetail,
                data: [ ]
            );
            $countJobs[ImportPhaseDetailJob::class]++;

            ImportPhaseDetailTranslationJob::dispatch(
                phase_detail: $findPhaseDetail,
                langId: 1,
                data: [
                    'name' => $ability['phaseDetails'],
                ]
            );
            $countJobs[ImportPhaseDetailTranslationJob::class]++;


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
        

        $keywords = DB::connection('mysql_aos')
        ->select("SELECT k.*,
                    wa.id AS ability_id,
                    wa.name AS ability_name
                FROM keyword k
                INNER JOIN warscroll_ability_keyword wak ON wak.keywordId = k.id
                INNER JOIN warscroll_ability wa ON wak.warscrollAbilityId = wa.id;
        ");
        foreach ($keywords as $keyword) {
            $keyword = (array) $keyword;

            ImportPivotAbilityKeywordJob::dispatch(
                ability: [
                    'slug' => StringTools::slug($keyword['ability_name']),
                    'warhammer_id' => $keyword['ability_id'],
                ],
                keyword: [
                    'slug' => StringTools::slug($keyword['name']),
                    'warhammer_id' => $keyword['id'],
                ],
            );
            $countJobs[ImportPivotAbilityKeywordJob::class]++;
        }


        $pivotsUnitAbility = DB::connection('mysql_aos')
        ->select("SELECT w.*,
                    wa.id AS ability_id,
                    wa.name AS ability_name
                FROM warscroll w
                INNER JOIN warscroll_ability wa ON wa.warscrollId = w.id;
        ");
        foreach ($pivotsUnitAbility as $pivotUnitAbility) {
            $pivotUnitAbility = (array) $pivotUnitAbility;

            ImportPivotUnitAbilityJob::dispatch(
                ability: [
                    'slug' => StringTools::slug($pivotUnitAbility['ability_name']),
                    'warhammer_id' => $pivotUnitAbility['ability_id'],
                ],
                unit: [
                    'slug' => StringTools::slug($pivotUnitAbility['name']),
                    'warhammer_id' => $pivotUnitAbility['id']
                ],
            );
            $countJobs[ImportPivotUnitAbilityJob::class]++;
        }

        foreach ($countJobs as $key => $countJob) {
            Log::info("".basename(__FILE__)."::".basename($key)." - $countJob jobs");
        }
    }
}