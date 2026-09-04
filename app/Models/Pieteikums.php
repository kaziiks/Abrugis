<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pieteikums extends Model
{
    protected $table = 'pieteikums';

protected $fillable = [
    'paving_type_id', 'client_name', 'client_email', 'client_phone',
    'project_description', 'area_m2', 'status', 'admin_notes'
];

public function pavingType()
{
    return $this->belongsTo(BrugaVeids::class);
}

public function review()
{
    return $this->hasOne(Atsauksme::class);
}
}
