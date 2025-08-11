<?php

namespace App\Console\Commands\ImportWarhammer\AoS;

use App\Jobs\ImportWarhammer\AoS\ImportFactionJob;
use App\Jobs\ImportWarhammer\AoS\ImportFactionTranslationJob;
use App\Jobs\ImportWarhammer\AoS\ImportPivotFactionUnitJob;
use App\Services\Utils\StringTools;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportFactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:aos:factions';

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
            ImportFactionJob::class => 0,
            ImportFactionTranslationJob::class => 0,
            ImportPivotFactionUnitJob::class => 0,
        ];

        
        $factions = DB::connection('mysql_aos')
        ->select("SELECT 
                    f.*
                FROM faction_keyword f;
        ");
        foreach ($factions as $faction) {
            $faction = (array) $faction;

            $findFaction = [
                'slug' => StringTools::slug($faction['name']),
                'warhammer_id' => $faction['id'],
            ];

            ImportFactionJob::dispatch(
                find: $findFaction,
                data: [
                    'factionHeaderImage' => $faction['factionHeaderImage'] ?? null,
                    'rosterHeaderImage' => $faction['rosterHeaderImage'] ?? null,
                    'selectFactionImage' => $faction['selectFactionImage'] ?? null,
                    'rosterFactionImage' => $faction['rosterFactionImage'] ?? null,
                    'moreInfoImage' => $faction['moreInfoImage'] ?? null,
                ]
            );
            $countJobs[ImportFactionJob::class]++;

            ImportFactionTranslationJob::dispatch(
                faction: $findFaction,
                langId: 1,
                data: [
                    'name' => $faction['name'],
                    'lore' => $faction['lore'] ?? null,
                ]
            );
            $countJobs[ImportFactionTranslationJob::class]++;
        }

        $factionUnits = DB::connection('mysql_aos')
        ->select("SELECT fk.id AS faction_id,
                    fk.name AS faction_name,
                    w.id AS unit_id,
                    w.name AS unit_name
                FROM warscroll w
                INNER JOIN warscroll_faction_keyword wfk ON w.id = wfk.warscrollId
                INNER JOIN faction_keyword fk ON wfk.factionKeywordId = fk.id;
        ");
        foreach ($factionUnits as $factionUnit) {
            $factionUnit = (array) $factionUnit;
            
            ImportPivotFactionUnitJob::dispatch(
                faction: [
                    "slug" => StringTools::slug($factionUnit["faction_name"]),
                    "warhammer_id" => $factionUnit["faction_id"],
                ],
                unit: [
                    "slug" => StringTools::slug($factionUnit["unit_name"]),
                    "warhammer_id" => $factionUnit["unit_id"],
                ],
            );
            $countJobs[ImportPivotFactionUnitJob::class]++;
        }


        foreach ($countJobs as $key => $countJob) {
            Log::info("".basename(__FILE__)."::".basename($key)." - $countJob jobs");
        }
    }
}