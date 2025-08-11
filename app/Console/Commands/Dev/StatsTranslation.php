<?php

namespace App\Console\Commands\Dev;

use App\Models\Language;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class StatsTranslation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:stats-translation {lang} {classes*}';

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
        $langCode = $this->argument('lang');
        $classes = $this->argument('classes');

        $refLang = Language::find(1);
        $targetLang = is_numeric($langCode) ? Language::find($langCode) : Language::where('code', 'en-US')->first();

        if (!$refLang || !$targetLang) {
            $this->error("Langue introuvable (en-US ou $langCode).");
            return;
        }

        foreach ($classes as $class) {
            $class = "App\Models\\".$class."Translation";

            if (!class_exists($class)) {
                $this->error("Classe '$class' introuvable.");
                continue;
            }

            $blankObject = new $class;
            $table = $blankObject->getTable() ?? null;
            if (!$table) {
                $this->error("Constante TABLE non définie dans '$class'.");
                continue;
            }

            $columns = $blankObject->getTranslatableFields();

            $refStats = $this->countNulls($table, $refLang->id, $columns);
            $targetStats = $this->countNulls($table, $targetLang->id, $columns);

            $percentStats = [];
            foreach ($columns as $col) {
                $total = $refStats["null_$col"] === 0 ? 0 : $refStats["total"];
                $missing = $targetStats["null_$col"] ?? 0;

                $percentStats["null_$col"] = $total > 0
                    ? round((($missing / $total) * 100), 2) . '%'
                    : 'N/A';
            }

            $this->info("Table : $table");
            $this->table(
                array_merge(['Lang'], array_map(fn ($c) => "NULL $c", $columns)),
                [
                    [$refLang->code, ...array_map(fn ($c) => $refStats["null_$c"], $columns)],
                    [$targetLang->code, ...array_map(fn ($c) => $targetStats["null_$c"], $columns)],
                    ['%', ...array_map(fn ($c) => $percentStats["null_$c"], $columns)],
                ]
            );
        }
    }

    private function countNulls($table, $langId, $columns)
    {
        $selects = [
            DB::raw('COUNT(*) as total'),
        ];
        foreach ($columns as $col) {
            $selects[] = DB::raw("COUNT(CASE WHEN `$col` IS NOT NULL THEN 1 END) AS null_$col");
        }

        return (array) DB::table($table)
            ->select($selects)
            ->where('lang_id', $langId)
            ->first();
    }
}