<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\Glossary;
use App\Models\GlossaryTranslation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportGlossaryTranslationJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $glossary;
    protected int $langId;
    protected array $data;

    public function __construct(array $glossary, int $langId, array $data)
    {
        $this->onQueue('imports');
        
        $this->glossary = $glossary;
        $this->langId = $langId;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = Glossary::query();
        foreach ($this->glossary as $key => $value) {
            $query->where($key, $value);
        }
        $glossary = $query->first();

        if (!$glossary) {
            $this->release($this->getReleaseDelay());
        }
        
        GlossaryTranslation::updateOrCreate(
            [
                'glossary_id' => $glossary->id,
                'lang_id' => $this->langId,
            ],
            $this->data
        );
    }
}
