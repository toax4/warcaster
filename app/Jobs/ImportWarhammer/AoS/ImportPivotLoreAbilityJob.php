<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Jobs\ImportWarhammer\AoS\AoSImportJob;
use App\Models\Ability;
use App\Models\Lore;
use App\Models\Pivots\PivotLoreAbility;
use App\Models\Pivots\PivotUnitAbility;
use App\Models\Unit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportPivotLoreAbilityJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $lore;
    protected array $ability;

    public function __construct(array $lore, array $ability)
    {
        $this->onQueue('imports');
        
        $this->lore = $lore;
        $this->ability = $ability;
    }

    public function handle()
    {
        $query = Ability::query();
        foreach ($this->ability as $key => $value) {
            $query->where($key, $value);
        }
        $ability = $query->first();
        
        $query = Lore::query();
        foreach ($this->lore as $key => $value) {
            $query->where($key, $value);
        }
        $lore = $query->first();

        if (!$ability || !$lore) {
            $this->release($this->getReleaseDelay());
        }

        PivotLoreAbility::updateOrCreate(
            [
                'lore_id' => $lore->id,
                'ability_id' => $ability->id,
            ]
        );
    }
}
