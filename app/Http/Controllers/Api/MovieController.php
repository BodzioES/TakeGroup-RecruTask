<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isNull;

class MovieController extends Controller
{
    //  Return a list of movies with translated data and genres
    public function index(Request $request){
        $acceptLanguage = $request->header('Accept-Language','en');
        $acceptedLocales = ['pl', 'en', 'de'];

        $locale = in_array($acceptLanguage, $acceptedLocales) ? $acceptLanguage : 'en';

        //  Initializing a query with Eager Loading (fetching related records immediately)
        $movies = Movie::with([
            //  Downloads movie descriptions (title and description) filtered by the selected language
            'languages' => function($query) use($locale){
                $query->where('locale', $locale);
            },
            //  Downloads genres and their nested translations, also filtered by language
            'genres.translations' => function($query) use($locale){
                $query->where('locale', $locale);
            }
        ])->paginate(10);

        return response()->json($movies);
    }
}
