<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\Ability;
use App\Models\AbilityTranslation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportAbilityTranslationJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $ability;
    protected int $langId;
    protected array $data;

    public function __construct(array $ability, int $langId, array $data)
    {
        $this->onQueue('imports');
        
        $this->ability = $ability;
        $this->langId = $langId;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = Ability::query();
        foreach ($this->ability as $key => $value) {
            $query->where($key, $value);
        }
        $ability = $query->first();

        if (!$ability) {
            $this->release($this->getReleaseDelay());
        }
        
        AbilityTranslation::updateOrCreate(
            [
                'ability_id' => $ability->id,
                'lang_id' => $this->langId,
            ],
            $this->data
        );
    }
}
