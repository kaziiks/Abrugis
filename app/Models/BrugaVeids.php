<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrugaVeids extends Model
{
    protected $table = 'bruga_veids';

    public function pieteikumi()
    {
        return $this->hasMany(Pieteikums::class);
    }
}
