<?php

namespace App\Jobs;

use App\Models\Genre;
use App\Models\Serie;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportSeriesData implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue, Dispatchable;

    public function __construct()
    {
        //
    }


    //  Downloads the 10 most popular TV series from the TMDB API, maps their genres and automatically
    //  builds a multilingual translation database for titles and descriptions in three languages (PL, EN, DE)
    public function handle(): void
    {
        $apiKey = config('services.tmdb.api_key') ?? env('TMDB_API_KEY');
        $locales = ['pl','en','de'];
        $totalSeriesProcessed = 0;

        $response = Http::get('https://api.themoviedb.org/3/tv/popular',[
            'api_key' => $apiKey,
            'page' => 1,
        ]);

        if ($response->failed()) {
            Log::error($response->json());
            return;
        }

        $serieList = $response->json()['results'];

        foreach ($serieList as $seriesData) {

            if ($totalSeriesProcessed >= 10){
                break;
            }

            $serieModel = Serie::updateOrCreate(
                ['external_id' => $seriesData['id']],
                [
                    'original_name' => $seriesData['original_name'],
                    'original_language' => $seriesData['original_language'],
                    'first_air_date' => $seriesData['first_air_date'] ?? null,
                    'popularity' => $seriesData['popularity'] ?? null,
                    'poster_path' => $seriesData['poster_path'],
                    'vote_average' => $seriesData['vote_average'] ?? 0,
                    'vote_count' => $seriesData['vote_count'] ?? 0,
                ]
            );

            //  Maps and synchronizes genres (removes old associations and creates new ones)
            if (isset($seriesData['genre_ids'])) {
                $localGenresIds = Genre::whereIn('external_id', $seriesData['genre_ids'])->pluck('id');
                $serieModel->genres()->sync($localGenresIds);
            }

            //  Loop to get detailed translations for each defined language
            foreach ($locales as $locale) {
                $detailsReponse = Http::get("https://api.themoviedb.org/3/tv/{$serieModel->external_id}",[
                    'api_key' => $apiKey,
                    'language' => $locale,
                ]);

                if($detailsReponse->successful()){
                    $details = $detailsReponse->json();

                    // Stores the translated name and description in the associated SerieLanguages table
                    $serieModel->languages()->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'name' => $details['name'] ?? $seriesData['name'],
                            'overview' => $details['overview'] ?? '',
                        ]
                    );
                }
            }
            $totalSeriesProcessed++;
        }
        Log::info('Total Series processed: '.$totalSeriesProcessed);
    }
}
