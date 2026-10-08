<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioImage extends Model
{
    protected $table = 'portfolio_images';

    protected $fillable = [
        'portfolio_info_id',
        'image_path',
    ];

    public function portfolioInfo()
    {
        return $this->belongsTo(PortfolioInfo::class);
    }
}