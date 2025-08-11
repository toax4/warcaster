<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\Keyword;
use App\Models\Pivots\PivotAbilityKeyword;
use App\Models\Ability;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportPivotAbilityKeywordJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $ability;
    protected array $keyword;

    public function __construct(array $ability, array $keyword)
    {
        $this->onQueue('imports');
        
        $this->ability = $ability;
        $this->keyword = $keyword;
    }

    public function handle()
    {
        $query = Keyword::query();
        foreach ($this->keyword as $key => $value) {
            $query->where($key, $value);
        }
        $keyword = $query->first();
        $query = Ability::query();
        foreach ($this->ability as $key => $value) {
            $query->where($key, $value);
        }
        $ability = $query->first();

        if (!$keyword || !$ability) {
            $this->release($this->getReleaseDelay());
        }

        PivotAbilityKeyword::updateOrCreate(
            [
                'ability_id' => $ability->id,
                'keyword_id' => $keyword->id,
            ]
        );
    }
}