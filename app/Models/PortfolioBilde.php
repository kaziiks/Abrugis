<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioBilde extends Model
{
    protected $table = 'portfolio_bilde';

    protected $fillable = [
        'portfolio_info_id',
        'image_path',
    ];

    public function portfolioInfo()
    {
        return $this->belongsTo(PortfolioInfo::class);
    }
}