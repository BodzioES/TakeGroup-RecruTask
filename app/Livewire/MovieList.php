<?php

namespace App\Livewire;

use App\Models\Movie;
use Livewire\Component;
use Livewire\WithPagination;

//  Dynamic display of a paginated list of movies with their translated data and genres
class MovieList extends Component
{
    use WithPagination;

    public $locale = 'en';

    public function changeLocale($newLocale){
        $this->locale = $newLocale;
        $this->resetPage();
    }

    public function render(){
        $movies = Movie::with([
            //  Filters video descriptions to only retrieve those in the currently set language
            'languages' => function($query){
                $query->where('locale',$this->locale);
            },
            //   Get related species and their names translated into the selected language
            'genres.translations' => function($query){
                $query->where('locale',$this->locale);
            }
        ])->paginate(5);

        return view('components.⚡movielist',compact('movies'));
    }
}
