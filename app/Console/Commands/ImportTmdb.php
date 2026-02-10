<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ImportTmdbData;

class ImportTmdb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tmdb:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download data from TMDB and save to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Importing data from TMDB');

        ImportTmdbData::dispatch();

        $this->info('The Job has been added to queue');
    }
}
