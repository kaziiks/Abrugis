<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PavingType extends Model
{
    protected $table = 'paving_types';

    protected $fillable = [
        'name',
        'price_per_m2',
        'description',
        'image',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'paving_type_id');
    }

    public function portfolioItems(): HasMany
    {
        return $this->hasMany(PortfolioInfo::class, 'paving_type_id');
    }
}
