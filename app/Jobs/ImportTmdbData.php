<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Movie;
use App\Models\MovieLanguages;

class ImportTmdbData implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, Dispatchable;

    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $apiKey = config('services.tmdb.api_key') ?? env('TMDB_API_KEY');

        $locales = ['pl','en','de'];

        $response = Http::get('https://api.themoviedb.org/3/movie/popular', [
            'api_key' => $apiKey,
        ]);

        if($response->failed()){
            Log::error($response->body());
            return;
        }

        $movies = $response->json()['results'];

        foreach ($movies as $movie) {
            $movie = Movie::updateOrCreate(
                ['external_id' => $movie['id']],
                [
                    'original_title' => $movie['title'],
                    'original_language' => $movie['original_language'],
                    'release_date' => $movie['release_date'] ?: null,
                    'poster_path' => $movie['poster_path'],
                    'vote_average' => $movie['vote_average'] ?? 0,
                    'vote_count' => $movie['vote_count'] ?? 0,
                ]
            );

            foreach ($locales as $locale) {
                $detailsResponse = Http::get("https://api.themoviedb.org/3/movie/{$movie->external_id}",[
                    'api_key' => $apiKey,
                    'language' => $locale,
                ]);

                if($detailsResponse->successful()){
                    $details = $detailsResponse->json();

                    $movie->languages()->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'title' => $details['title'],
                            'overview' => $details['overview'],
                        ]
                    );
                }
            }
        }
        Log::info("Dwonloaded and translated " . count($movies) . ' movies');
    }
}
