<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Serie extends Model
{

    protected $guarded = [];
    public function genres(){
        return $this->belongsToMany(Genre::class,'genre_series');
    }

    public function languages(): HasMany{
        return $this->hasMany(SerieLanguages::class);
    }
}
