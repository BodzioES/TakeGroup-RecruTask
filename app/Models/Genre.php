<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Genre extends Model
{

    protected $guarded = [];

    public function translations():HasMany
    {
        return $this->hasMany(GenreLanguages::class);
    }
    public function movies(){
        return $this->belongsToMany(Movie::class,'genre_movies')->withTimestamps();
    }

    public function series(){
        return $this->belongsToMany(Serie::class,'genre_series')->withTimestamps();
    }
}
