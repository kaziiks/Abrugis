<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'application_id',
        'author_name',
        'rating',
        'review',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
