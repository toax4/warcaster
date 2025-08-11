<?php

namespace App\Jobs\ImportWarhammer\AoS;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AoSImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 15;

    protected function getReleaseDelay()
    {
        return now()->addMinutes(2);
    }

    // /**
    //  * Create a new job instance.
    //  */
    // public function __construct()
    // {
    //     //
    // }

    // /**
    //  * Execute the job.
    //  */
    // public function handle(): void
    // {
    //     //
    // }
}