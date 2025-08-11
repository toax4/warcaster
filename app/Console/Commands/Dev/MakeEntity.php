<?php

namespace App\Console\Commands\Dev;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MakeEntity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:make-entity {entity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $entity = $this->argument('entity');

        Artisan::call("make:model $entity -a");
        Artisan::call("make:command Imports/Import{$entity}");
        Artisan::call("make:resource {$entity}Resource");
    }
}