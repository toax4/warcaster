<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\Unit;
use App\Models\UnitTranslation;
use App\Models\Weapon;
use App\Models\WeaponTranslation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportWeaponTranslationJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $weapon;
    protected int $langId;
    protected array $data;

    public function __construct(array $weapon, int $langId, array $data)
    {
        $this->onQueue('imports');
        
        $this->weapon = $weapon;
        $this->langId = $langId;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = Weapon::query();
        foreach ($this->weapon as $key => $value) {
            $query->where($key, $value);
        }
        $weapon = $query->first();

        if (!$weapon) {
            $this->release($this->getReleaseDelay());
        }
        
        WeaponTranslation::updateOrCreate(
            [
                'weapon_id' => $weapon->id,
                'lang_id' => $this->langId,
            ],
            $this->data
        );
    }
}
