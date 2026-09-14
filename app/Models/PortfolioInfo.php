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
        'bruga_veids_id',
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

    public function brugaVeids(): BelongsTo
    {
        return $this->belongsTo(BrugaVeids::class, 'bruga_veids_id');
    }

    public function bildes(): HasMany
    {
        return $this->hasMany(PortfolioBilde::class);
    }
}