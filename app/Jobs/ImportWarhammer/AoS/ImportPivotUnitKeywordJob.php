<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\Keyword;
use App\Models\Pivots\PivotUnitKeyword;
use App\Models\Unit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportPivotUnitKeywordJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $unit;
    protected array $keyword;

    public function __construct(array $unit, array $keyword)
    {
        $this->onQueue('imports');
        
        $this->unit = $unit;
        $this->keyword = $keyword;
    }

    public function handle()
    {
        $query = Keyword::query();
        foreach ($this->keyword as $key => $value) {
            $query->where($key, $value);
        }
        $keyword = $query->first();
        $query = Unit::query();
        foreach ($this->unit as $key => $value) {
            $query->where($key, $value);
        }
        $unit = $query->first();

        if (!$keyword || !$unit) {
            $this->release($this->getReleaseDelay());
        }

        PivotUnitKeyword::updateOrCreate(
            [
                'unit_id' => $unit->id,
                'keyword_id' => $keyword->id,
            ]
        );
    }
}
