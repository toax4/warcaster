<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\Unit;
use App\Models\UnitTranslation;
use App\Services\Utils\StringTools;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportUnitJob extends AoSImportJob implements ShouldQueue
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
        $unit = Unit::updateOrCreate(
            $this->find,
            $this->data,
        );
    }
}
