<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\Ability;
use App\Models\BattleFormation;
use App\Models\Pivots\PivotBattleFormationAbility;
use App\Models\Pivots\PivotUnitAbility;
use App\Models\Unit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportPivotBattleFormationAbilityJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $battleFormation;
    protected array $ability;

    public function __construct(array $battleFormation, array $ability)
    {
        $this->onQueue('imports');
        
        $this->battleFormation = $battleFormation;
        $this->ability = $ability;
    }

    public function handle()
    {
        $query = Ability::query();
        foreach ($this->ability as $key => $value) {
            $query->where($key, $value);
        }
        $ability = $query->first();
        
        $query = BattleFormation::query();
        foreach ($this->battleFormation as $key => $value) {
            $query->where($key, $value);
        }
        $battleFormation = $query->first();

        if (!$ability || !$battleFormation) {
            $this->release($this->getReleaseDelay());
        }

        PivotBattleFormationAbility::updateOrCreate(
            [
                'battle_formation_id' => $battleFormation->id,
                'ability_id' => $ability->id,
            ]
        );
    }
}