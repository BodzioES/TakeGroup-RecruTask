<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GenreLanguages extends Model
{

    protected $guarded = [];

    public function genre(): BelongsTo{
        return $this->belongsTo(Genre::class);
    }
}
