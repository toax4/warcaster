<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\Ability;
use App\Models\Phase;
use App\Models\PhaseDetail;
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
        if (isset($this->data["phase"]) && !empty($this->data["phase"])) {
            $phase = Phase::firstOrCreate(["slug" => StringTools::slug(Str::snake($this->data["phase"]))]);

            if ($phase->wasRecentlyCreated) {
                ImportPhaseTranslationJob::dispatch(
                    phase: [
                        "slug" => $phase->slug,
                        "id" => $phase->id,
                    ],
                    langId: 1,
                    data: [
                        'name' => $this->data['phase'],
                    ]
                );
            }

            // dd($this->data["phase"], StringTools::slug(Str::snake($this->data["phase"])) ,$phase, $this->data);
            $this->data["phase_id"] = $phase !== null ? $phase->id : null;
        }

        if (isset($this->data["phase_detail"]) && !empty($this->data["phase_detail"])) {
            $phaseDetail = PhaseDetail::firstOrCreate(["slug" => StringTools::slug(Str::snake($this->data["phase_detail"]))]);

            if ($phaseDetail->wasRecentlyCreated) {
                ImportPhaseDetailTranslationJob::dispatch(
                    phase_detail: [
                        "slug" => $phaseDetail->slug,
                        "id" => $phaseDetail->id,
                    ],
                    langId: 1,
                    data: [
                        'name' => $this->data['phase_detail'],
                    ]
                );
            }

            // dd($this->data["phase_id"], StringTools::slug(Str::snake($this->data["phase_id"])) ,$phaseDetail, $this->data);
            $this->data["phase_detail_id"] = $phaseDetail !== null ? $phaseDetail->id : null;
        }
        
        $unit = Ability::updateOrCreate(
            $this->find,
            $this->data,
        );
    }
}