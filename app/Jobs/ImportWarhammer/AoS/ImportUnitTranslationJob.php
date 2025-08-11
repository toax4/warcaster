<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\Unit;
use App\Models\UnitTranslation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportUnitTranslationJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $unit;
    protected int $langId;
    protected array $data;

    public function __construct(array $unit, int $langId, array $data)
    {
        $this->onQueue('imports');
        
        $this->unit = $unit;
        $this->langId = $langId;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = Unit::query();
        foreach ($this->unit as $key => $value) {
            $query->where($key, $value);
        }
        $unit = $query->first();

        if (!$unit) {
            $this->release($this->getReleaseDelay());
        }
        
        UnitTranslation::updateOrCreate(
            [
                'unit_id' => $unit->id,
                'lang_id' => $this->langId,
            ],
            $this->data
        );
    }
}
