<?php

namespace App\Jobs\ImportWarhammer\AoS;

use App\Models\Keyword;
use App\Models\KeywordTranslation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportKeywordTranslationJob extends AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $keyword;
    protected int $langId;
    protected array $data;

    public function __construct(array $keyword, int $langId, array $data)
    {
        $this->onQueue('imports');
        
        $this->keyword = $keyword;
        $this->langId = $langId;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = Keyword::query();
        foreach ($this->keyword as $key => $value) {
            $query->where($key, $value);
        }
        $keyword = $query->first();

        if (!$keyword) {
            $this->release($this->getReleaseDelay());
        }
        
        KeywordTranslation::updateOrCreate(
            [
                'keyword_id' => $keyword->id,
                'lang_id' => $this->langId,
            ],
            $this->data
        );
    }
}
