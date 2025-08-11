<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Jobs\ImportWarhammer\AoS\AoSImportJob;
use App\Models\Ability;
use App\Models\Faction;
use App\Models\Pivots\PivotUnitAbility;
use App\Models\Pivots\PivotUnitFaction;
use App\Models\Unit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportPivotFactionUnitJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $unit;
    protected array $faction;

    public function __construct(array $unit, array $faction)
    {
        $this->onQueue('imports');
        
        $this->unit = $unit;
        $this->faction = $faction;
    }

    public function handle()
    {
        $query = Faction::query();
        foreach ($this->faction as $key => $value) {
            $query->where($key, $value);
        }
        $faction = $query->first();
        
        $query = Unit::query();
        foreach ($this->unit as $key => $value) {
            $query->where($key, $value);
        }
        $unit = $query->first();

        if (!$faction || !$unit) {
            $this->release($this->getReleaseDelay());
        }

        PivotUnitFaction::updateOrCreate(
            [
                'unit_id' => $unit->id,
                'faction_id' => $faction->id,
            ]
        );
    }
}
