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
use App\Models\Genre;

class ImportTmdbData implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, Dispatchable;

    public function __construct()
    {
        //
    }


    public function handle(): void
    {
        $apiKey = config('services.tmdb.api_key') ?? env('TMDB_API_KEY');
        $locales = ['pl', 'en', 'de'];
        $totalMoviesProcessed = 0;


        for($page = 1; $page <= 3; $page++){
            $response = Http::get('https://api.themoviedb.org/3/movie/popular', [
                'api_key' => $apiKey,
                'page' => $page,
            ]);

            if($response->failed()){
                Log::error($page . ": ". $response->body());
                continue;
            }

            $movies = $response->json()['results'];

            foreach ($movies as $movieData) {

                if ($totalMoviesProcessed >= 50){
                    break 2;
                }

                $movieModel = Movie::updateOrCreate(
                    ['external_id' => $movieData['id']],
                    [
                        'original_title' => $movieData['title'],
                        'original_language' => $movieData['original_language'],
                        'release_date' => $movieData['release_date'] ?: null,
                        'poster_path' => $movieData['poster_path'],
                        'vote_average' => $movieData['vote_average'] ?? 0,
                        'vote_count' => $movieData['vote_count'] ?? 0,
                    ]
                );

                if (isset($movieData['genre_ids'])) {
                    $localGenreIds = Genre::whereIn('external_id', $movieData['genre_ids'])->pluck('id');
                    $movieModel->genres()->sync($localGenreIds);
                }

                foreach ($locales as $locale) {
                    $detailsResponse = Http::get("https://api.themoviedb.org/3/movie/{$movieModel->external_id}",[
                        'api_key' => $apiKey,
                        'language' => $locale,
                    ]);

                    if($detailsResponse->successful()){
                        $details = $detailsResponse->json();

                        $movieModel->languages()->updateOrCreate(
                            ['locale' => $locale],
                            [
                                'title' => $details['title'] ?? $movieData['title'],
                                'overview' => $details['overview'] ?? '',
                            ]
                        );
                    }
                }
                $totalMoviesProcessed++;
            }
        }
        Log::info("Downloaded, translated and synced genres for " . $totalMoviesProcessed . ' movies');
    }
}
