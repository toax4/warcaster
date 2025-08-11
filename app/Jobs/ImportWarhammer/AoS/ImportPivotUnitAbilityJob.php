<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\Ability;
use App\Models\Pivots\PivotUnitAbility;
use App\Models\Unit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportPivotUnitAbilityJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $unit;
    protected array $ability;

    public function __construct(array $unit, array $ability)
    {
        $this->onQueue('imports');
        
        $this->unit = $unit;
        $this->ability = $ability;
    }

    public function handle()
    {
        $query = Ability::query();
        foreach ($this->ability as $key => $value) {
            $query->where($key, $value);
        }
        $ability = $query->first();
        
        $query = Unit::query();
        foreach ($this->unit as $key => $value) {
            $query->where($key, $value);
        }
        $unit = $query->first();

        if (!$ability || !$unit) {
            $this->release($this->getReleaseDelay());
        }

        PivotUnitAbility::updateOrCreate(
            [
                'unit_id' => $unit->id,
                'ability_id' => $ability->id,
            ]
        );
    }
}
