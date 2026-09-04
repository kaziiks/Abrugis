<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atsauksme extends Model
{
    protected $table = 'atsauksme';

    public function pieteikums()
    {
        return $this->belongsTo(Pieteikums::class);
    }
}
