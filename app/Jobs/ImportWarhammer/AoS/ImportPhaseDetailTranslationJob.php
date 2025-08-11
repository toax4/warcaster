<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\PhaseDetail;
use App\Models\PhaseDetailTranslation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportPhaseDetailTranslationJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $phase_detail;
    protected int $langId;
    protected array $data;

    public function __construct(array $phase_detail, int $langId, array $data)
    {
        $this->onQueue('imports');
        
        $this->phase_detail = $phase_detail;
        $this->langId = $langId;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = PhaseDetail::query();
        foreach ($this->phase_detail as $key => $value) {
            $query->where($key, $value);
        }
        $phase_detail = $query->first();

        if (!$phase_detail) {
            $this->release($this->getReleaseDelay());
        }
        
        PhaseDetailTranslation::updateOrCreate(
            [
                'phase_detail_id' => $phase_detail->id,
                'lang_id' => $this->langId,
            ],
            $this->data
        );
    }
}
