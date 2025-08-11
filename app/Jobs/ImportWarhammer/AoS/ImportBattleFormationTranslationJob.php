<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Jobs\ImportWarhammer\AoS\AoSImportJob;
use App\Models\Ability;
use App\Models\AbilityTranslation;
use App\Models\BattleFormation;
use App\Models\BattleFormationTranslation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportBattleFormationTranslationJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $battleFormation;
    protected int $langId;
    protected array $data;

    public function __construct(array $battleFormation, int $langId, array $data)
    {
        $this->onQueue('imports');
        
        $this->battleFormation = $battleFormation;
        $this->langId = $langId;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = BattleFormation::query();
        foreach ($this->battleFormation as $key => $value) {
            $query->where($key, $value);
        }
        $battleFormation = $query->first();

        if (!$battleFormation) {
            $this->release($this->getReleaseDelay());
        }
        
        BattleFormationTranslation::updateOrCreate(
            [
                'battle_formation_id' => $battleFormation->id,
                'lang_id' => $this->langId,
            ],
            $this->data
        );
    }
}
