<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SerieLanguages extends Model
{

    protected $guarded = [];
    public function serie(): BelongsTo{
        return $this->belongsTo(Serie::class);
    }
}
