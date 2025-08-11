<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\Ability;
use App\Models\Pivots\PivotUnitAbility;
use App\Models\Pivots\PivotUnitWeapon;
use App\Models\Pivots\PivotWeaponWeaponAbility;
use App\Models\Unit;
use App\Models\Weapon;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportPivotUnitWeaponJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $unit;
    protected array $weapon;

    public function __construct(array $unit, array $weapon)
    {
        $this->onQueue('imports');
        
        $this->unit = $unit;
        $this->weapon = $weapon;
    }

    public function handle()
    {
        $query = Weapon::query();
        foreach ($this->weapon as $key => $value) {
            $query->where($key, $value);
        }
        $weapon = $query->first();
        
        $query = Unit::query();
        foreach ($this->unit as $key => $value) {
            $query->where($key, $value);
        }
        $unit = $query->first();

        if (!$weapon || !$unit) {
            $this->release($this->getReleaseDelay());
        }

        PivotUnitWeapon::updateOrCreate(
            [
                'unit_id' => $unit->id,
                'weapon_id' => $weapon->id,
            ]
        );
    }
}