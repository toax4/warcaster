<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\Faction;
use App\Models\Lore;
use App\Models\Pivots\PivotFactionLore;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportPivotFactionLoreJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $lore;
    protected array $faction;

    public function __construct(array $lore, array $faction)
    {
        $this->onQueue('imports');
        
        $this->lore = $lore;
        $this->faction = $faction;
    }

    public function handle()
    {
        $query = Faction::query();
        foreach ($this->faction as $key => $value) {
            $query->where($key, $value);
        }
        $faction = $query->first();
        
        $query = Lore::query();
        foreach ($this->lore as $key => $value) {
            $query->where($key, $value);
        }
        $lore = $query->first();

        if (!$faction || !$lore) {
            $this->release($this->getReleaseDelay());
        }

        PivotFactionLore::updateOrCreate(
            [
                'lore_id' => $lore->id,
                'faction_id' => $faction->id,
            ]
        );
    }
}
