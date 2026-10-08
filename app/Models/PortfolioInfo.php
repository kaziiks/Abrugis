<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PortfolioInfo extends Model
{
    protected $table = 'portfolio_info';

    protected $fillable = [
        'user_id',
        'paving_type_id',
        'title',
        'description',
        'city',
        'area_m2',
        'completed_year',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pavingType(): BelongsTo
    {
        return $this->belongsTo(PavingType::class, 'paving_type_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PortfolioImage::class);
    }
}