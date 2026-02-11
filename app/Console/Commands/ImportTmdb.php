<?php

namespace App\Console\Commands;

use App\Jobs\ImportGenres;
use App\Jobs\ImportSeriesData;
use Illuminate\Console\Command;
use App\Jobs\ImportMoviesData;

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
        $this->info('Importing genres first');
        ImportGenres::dispatchSync();

        $this->info('Importing movies from TMDB');
        ImportMoviesData::dispatchSync();

        $this->info('Importing series from TMDB');
        ImportSeriesData::dispatchSync();

        $this->info('The Job has been added to queue');
    }
}
