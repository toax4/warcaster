<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\Ability;
use App\Models\Phase;
use App\Services\Utils\StringTools;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportAbilityJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected array $find;
    protected array $data;

    public function __construct(array $find, array $data)
    {
        $this->onQueue('imports');
        
        $this->find = $find;
        $this->data = $data;
    }

    public function handle()
    {
        // dd($this->data);
        if (isset($this->data["phase_id"]) && !empty($this->data["phase_id"])) {
            $phase = Phase::firstOrCreate(["slug" => StringTools::slug(Str::snake($this->data["phase_id"]))]);

            if ($phase->wasRecentlyCreated) {
                ImportPhaseTranslationJob::dispatch(
                    phase: [
                        "slug" => $phase->slug,
                        "id" => $phase->id,
                    ],
                    langId: 1,
                    data: [
                        'name' => $this->data['phase_id'],
                    ]
                );
            }

            // dd($this->data["phase_id"], StringTools::slug(Str::snake($this->data["phase_id"])) ,$phase, $this->data);
            $this->data["phase_id"] = $phase !== null ? $phase->id : null;
        }
        
        $unit = Ability::updateOrCreate(
            $this->find,
            $this->data,
        );
    }
}
