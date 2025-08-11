<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\Ability;
use App\Models\Pivots\PivotUnitAbility;
use App\Models\Pivots\PivotUnitWeapon;
use App\Models\Pivots\PivotWeaponWeaponAbility;
use App\Models\Unit;
use App\Models\Weapon;
use App\Models\WeaponAbility;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportPivotWeaponWeaponAbilityJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $weaponAbility;
    protected array $weapon;
    protected array $data;

    public function __construct(array $weaponAbility, array $weapon, array $data)
    {
        $this->onQueue('imports');
        
        $this->weaponAbility = $weaponAbility;
        $this->weapon = $weapon;
        $this->data = $data;
    }

    public function handle()
    {
        $query = Weapon::query();
        foreach ($this->weapon as $key => $value) {
            $query->where($key, $value);
        }
        $weapon = $query->first();
        
        $query = WeaponAbility::query();
        foreach ($this->weaponAbility as $key => $value) {
            $query->where($key, $value);
        }
        $weaponAbility = $query->first();

        if (!$weapon || !$weaponAbility) {
            $this->release($this->getReleaseDelay());
        }

        PivotWeaponWeaponAbility::updateOrCreate(
            [
                'weapon_ability_id' => $weaponAbility->id,
                'weapon_id' => $weapon->id,
            ],
            $this->data
        );
    }
}
