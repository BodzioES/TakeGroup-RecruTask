<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Serie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SerieController extends Controller
{
    //  Returns a list of series and selected language translations
    public function index(Request $request): JsonResponse{

        $acceptedLocales = ['pl','en','de'];
        $acceptLanguage = $request->header('Accept-Language','en');
        $locale = in_array($acceptLanguage, $acceptedLocales) ? $acceptLanguage : 'en';

        //  building a query using Eager Loading
        $series = Serie::with([
            //  Downloading translated show details (title adn description) for the selected language
            'details' => function($query) use ($locale){
                $query->where('locale', $locale);
            },
            //  Download related genres along with their translations filtered by language
            'genres.translations' => function($query) use ($locale){
                $query->where('locale', $locale);
            }
        ])->paginate(10);

        return response()->json($series);

    }
}
