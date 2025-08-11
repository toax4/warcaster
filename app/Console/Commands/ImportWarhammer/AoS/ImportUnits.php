<?php

namespace App\Console\Commands\ImportWarhammer\AoS;

use App\Jobs\ImportWarhammer\AoS\ImportPivotUnitKeywordJob;
use App\Jobs\ImportWarhammer\AoS\ImportUnitJob;
use App\Jobs\ImportWarhammer\AoS\ImportUnitTranslationJob;
use App\Services\Utils\StringTools;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportUnits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:aos:units';

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
            ImportUnitJob::class => 0,
            ImportUnitTranslationJob::class => 0,
            ImportPivotUnitKeywordJob::class => 0,
        ];


        $units = DB::connection('mysql_aos')
        ->select("SELECT 
                    w.*
                FROM warscroll w;
        ");
        foreach ($units as $unit) {
            $unit = (array) $unit;

            $findUnit = [
                'slug' => StringTools::slug($unit['name']),
                'warhammer_id' => $unit['id'],
            ];

            ImportUnitJob::dispatch(
                find: $findUnit,
                data: [
                   'move' => $unit['move'],
                   'save' => $unit['save'],
                   'control' => $unit['control'],
                   'health' => $unit['health'],
                   'points' => $unit['points'],
                   'bannerImage' => $unit['bannerImage'],
                   'rowImage' => $unit['rowImage'],
                ]
            );
            $countJobs[ImportUnitJob::class]++;

            ImportUnitTranslationJob::dispatch(
                unit: $findUnit,
                langId: 1,
                data: [
                    'name' => $unit['name'],
                    'subname' => $unit['subname'] ?? null,
                    'lore' => $unit['lore'] ?? null,
                ]
            );
            $countJobs[ImportUnitTranslationJob::class]++;

            foreach (explode(',', $unit['referenceKeywords']) as $keyword) {
                $keyword = trim($keyword);

                ImportPivotUnitKeywordJob::dispatch(
                    unit: $findUnit,
                    keyword: [
                        "slug" => StringTools::slug($keyword),
                    ],
                );
                $countJobs[ImportPivotUnitKeywordJob::class]++;
            }
        }
        
        
        foreach ($countJobs as $key => $countJob) {
            Log::info("".basename(__FILE__)."::".basename($key)." - $countJob jobs");
        }
    }
}