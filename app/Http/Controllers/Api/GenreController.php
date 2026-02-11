<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GenreController extends Controller
{
    //  Get  list of genres based on the selected language
    public function index(Request $request): JsonResponse
    {
        $acceptedLocales = ['pl', 'en', 'de'];
        $acceptedLanguage = $request->header('Accept-Language', 'en');
        $locale = in_array($acceptedLanguage, $acceptedLocales) ? $acceptedLanguage : 'en';

        //  Builds a query to the database: loads genres with their translations
        $genres = Genre::with([
            'translations' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            }
        ])->paginate(10);

        return response()->json($genres);
    }
}
