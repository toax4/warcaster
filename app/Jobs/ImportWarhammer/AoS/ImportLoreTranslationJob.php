<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\Lore;
use App\Models\LoreTranslation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportLoreTranslationJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $lore;
    protected int $langId;
    protected array $data;

    public function __construct(array $lore, int $langId, array $data)
    {
        $this->onQueue('imports');
        
        $this->lore = $lore;
        $this->langId = $langId;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = Lore::query();
        foreach ($this->lore as $key => $value) {
            $query->where($key, $value);
        }
        $lore = $query->first();

        if (!$lore) {
            $this->release($this->getReleaseDelay());
        }
        
        LoreTranslation::updateOrCreate(
            [
                'lore_id' => $lore->id,
                'lang_id' => $this->langId,
            ],
            $this->data
        );
    }
}
