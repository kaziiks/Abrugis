<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioInfo extends Model
{
    protected $table = 'portfolio_info';

    protected $fillable = [
        'bruga_veids_id',
        'title',
        'description',
        'city',
        'area_m2',
        'completed_year',
    ];

    public function brugaVeids()
    {
        return $this->belongsTo(BrugaVeids::class);
    }

    public function bildes()
    {
        return $this->hasMany(PortfolioBilde::class);
    }
}