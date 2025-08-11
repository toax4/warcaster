<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\Phase;
use App\Models\PhaseTranslation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportPhaseTranslationJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $phase;
    protected int $langId;
    protected array $data;

    public function __construct(array $phase, int $langId, array $data)
    {
        $this->onQueue('imports');
        
        $this->phase = $phase;
        $this->langId = $langId;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = Phase::query();
        foreach ($this->phase as $key => $value) {
            $query->where($key, $value);
        }
        $phase = $query->first();

        if (!$phase) {
            $this->release($this->getReleaseDelay());
        }
        
        PhaseTranslation::updateOrCreate(
            [
                'phase_id' => $phase->id,
                'lang_id' => $this->langId,
            ],
            $this->data
        );
    }
}
