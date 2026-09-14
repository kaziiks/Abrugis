<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Atsauksme extends Model
{
    protected $table = 'atsauksme';

    protected $fillable = [
        'pieteikums_id',
        'author_name',
        'rating',
        'atsauksme',
    ];

    public function pieteikums(): BelongsTo
    {
        return $this->belongsTo(Pieteikums::class);
    }
}
