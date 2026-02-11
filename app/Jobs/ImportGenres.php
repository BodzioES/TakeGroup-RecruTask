<?php

namespace App\Jobs;

use App\Models\Genre;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportGenres implements ShouldQueue
{
    use Queueable,Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct()
    {
        //
    }


    //  Downloading and saving genres and their languages
    public function handle(): void
    {
        $apiKey = config('services.tmdb.api_key') ?? env('TMDB_API_KEY');
        $locales = ['pl','en','de'];
        $types = ['movie','tv'];


        foreach ($locales as $locale) {
            foreach ($types as $type) {
                $response = Http::get("https://api.themoviedb.org/3/genre/{$type}/list",[
                    'api_key' => $apiKey,
                    'language' => $locale,
                ]);

                if ($response->successful()) {
                    $genres = $response->json()['genres'];

                    foreach ($genres as $genreData) {
                        $genreModel = Genre::updateOrCreate(
                            ['external_id' => $genreData['id']],
                        );

                        //  Creates or updates a translation name for a given language of a species
                        $genreModel->translations()->updateOrCreate(
                            ['locale' => $locale],
                            ['name' => $genreData['name'] ?? 'No name']
                        );
                    }
                }
            }

        }
        Log::info('All genres synchronized in 3 languages');
    }
}
