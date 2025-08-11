<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\Ability;
use App\Models\Faction;
use App\Models\HeroicTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportHeroicTraitJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $faction;
    protected array $ability;

    public function __construct(array $faction, array $ability)
    {
        $this->onQueue('imports');
        
        $this->faction = $faction;
        $this->ability = $ability;
    }

    public function handle()
    {
        $query = Ability::query();
        foreach ($this->ability as $key => $value) {
            $query->where($key, $value);
        }
        $ability = $query->first();
        
        $query = Faction::query();
        foreach ($this->faction as $key => $value) {
            $query->where($key, $value);
        }
        $faction = $query->first();

        if (!$ability || !$faction) {
            $this->release($this->getReleaseDelay());
        }

        HeroicTrait::updateOrCreate(
            [
                'faction_id' => $faction->id,
                'ability_id' => $ability->id,
            ]
        );
    }
}