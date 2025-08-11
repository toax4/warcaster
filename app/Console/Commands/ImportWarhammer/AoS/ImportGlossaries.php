<?php

namespace App\Console\Commands\ImportWarhammer\AoS;

use App\Jobs\ImportWarhammer\AoS\ImportPivotGlossaryKeywordJob;
use App\Jobs\ImportWarhammer\AoS\ImportGlossaryJob;
use App\Jobs\ImportWarhammer\AoS\ImportGlossaryTranslationJob;
use App\Services\Utils\StringTools;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportGlossaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:aos:glossaries';

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
            ImportGlossaryJob::class => 0,
            ImportGlossaryTranslationJob::class => 0,
        ];


        $glossaries = DB::connection('mysql_aos')
        ->select("SELECT rc.title AS name,
                    rcc.textContent AS content
                FROM rule_container rc
                LEFT JOIN rule_container_component rcc ON rcc.ruleContainerId = rc.id
                LEFT JOIN rule_section rs ON rs.id = rc.ruleSectionId
                WHERE rs.name = 'Glossary'
                ORDER BY rc.title
        ");
        foreach ($glossaries as $glossary) {
            $glossary = (array) $glossary;

            $findGlossary = [
                'slug' => StringTools::slug($glossary['name']),
            ];

            ImportGlossaryJob::dispatch(
                find: $findGlossary,
                data: []
            );
            $countJobs[ImportGlossaryJob::class]++;

            ImportGlossaryTranslationJob::dispatch(
                glossary: $findGlossary,
                langId: 1,
                data: [
                    'name' => $glossary['name'],
                    'content' => $glossary['content'] ?? null,
                ]
            );
            $countJobs[ImportGlossaryTranslationJob::class]++;
        }
        
        
        foreach ($countJobs as $key => $countJob) {
            Log::info("".basename(__FILE__)."::".basename($key)." - $countJob jobs");
        }
    }
}
