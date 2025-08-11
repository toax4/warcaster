<?php

namespace App\Console\Commands\ImportWarhammer\AoS;

use App\Jobs\ImportWarhammer\AoS\ImportKeywordJob;
use App\Jobs\ImportWarhammer\AoS\ImportKeywordTranslationJob;
use App\Services\Utils\StringTools;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportKeywords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:aos:keywords';

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
            ImportKeywordJob::class => 0,
            ImportKeywordTranslationJob::class => 0,
        ];

        
        $keywords = DB::connection('mysql_aos')
        ->select("SELECT 
                    k.*
                FROM keyword k;
        ");
        foreach ($keywords as $keyword) {
            $keyword = (array) $keyword;

            $findKeyword = [
                'slug' => StringTools::slug($keyword['name']),
                'warhammer_id' => $keyword['id'],
            ];

            ImportKeywordJob::dispatch(
                find: $findKeyword,
                data: [
                    'warhammer_id' => $keyword['id'],
                ]
            );
            $countJobs[ImportKeywordJob::class]++;

            ImportKeywordTranslationJob::dispatch(
                keyword: $findKeyword,
                langId: 1,
                data: [
                    'label' => $keyword['name'],
                ],
            );
            $countJobs[ImportKeywordTranslationJob::class]++;
        }

        $keywords = [];
        $keywordsHeroes = DB::connection('mysql_aos')
        ->select("SELECT 
                    k.referenceKeywords
                FROM warscroll k;
        ");
        foreach ($keywordsHeroes as $key => $hero) {
            $keys = explode(',', $hero->referenceKeywords);
            foreach ($keys as $k => $key) {
                $keywords[] = [
                    'name' => trim($key),
                    'id'   => null
                ];
            }
        }
        $keywords = collect($keywords)->unique('name')->values()->all();
        foreach ($keywords as $keyword) {
            $keyword = (array) $keyword;

            $findKeyword = [
                'slug' => StringTools::slug($keyword['name']),
            ];

            ImportKeywordJob::dispatch(
                find: $findKeyword,
                data: [
                ]
            );
            $countJobs[ImportKeywordJob::class]++;

            ImportKeywordTranslationJob::dispatch(
                keyword: $findKeyword,
                langId: 1,
                data: [
                    'label' => $keyword['name'],
                ],
            );
            $countJobs[ImportKeywordTranslationJob::class]++;
        }


        foreach ($countJobs as $key => $countJob) {
            Log::info("".basename(__FILE__)."::".basename($key)." - $countJob jobs");
        }
    }
}