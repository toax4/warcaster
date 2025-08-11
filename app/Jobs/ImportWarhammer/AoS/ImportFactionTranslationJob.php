<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\Faction;
use App\Models\FactionTranslation;
use App\Models\Unit;
use App\Models\UnitTranslation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportFactionTranslationJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $faction;
    protected int $langId;
    protected array $data;

    public function __construct(array $faction, int $langId, array $data)
    {
        $this->onQueue('imports');
        
        $this->faction = $faction;
        $this->langId = $langId;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = Faction::query();
        foreach ($this->faction as $key => $value) {
            $query->where($key, $value);
        }
        $faction = $query->first();

        if (!$faction) {
            $this->release($this->getReleaseDelay());
        }
        
        FactionTranslation::updateOrCreate(
            [
                'faction_id' => $faction->id,
                'lang_id' => $this->langId,
            ],
            $this->data
        );
    }
}