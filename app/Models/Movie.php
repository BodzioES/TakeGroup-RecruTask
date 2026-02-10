<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{

    protected $guarded = [];
    public function genres(){
        return $this->belongsToMany(Genre::class,'genre_movie');
    }

    public function languages(): HasMany{
        return $this->hasMany(MovieLanguages::class);
    }
}
