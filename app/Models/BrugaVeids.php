<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BrugaVeids extends Model
{
    protected $table = 'bruga_veids';

    public function pieteikumi(): HasMany
    {
        return $this->hasMany(Pieteikums::class, 'bruga_veids_id');
    }

    public function portfolioItems(): HasMany
    {
        return $this->hasMany(PortfolioInfo::class, 'bruga_veids_id');
    
}
}