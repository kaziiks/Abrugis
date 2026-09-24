<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pieteikums extends Model
{
    protected $table = 'pieteikums';

    protected $casts = [
        'requested_date' => 'date',
    ];

    protected $fillable = [
        'user_id',
        'bruga_veids_id',
        'client_name',
        'client_email',
        'client_phone',
        'project_description',
        'area_m2',
        'requested_date',
        'status',
        'admin_notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pavingType(): BelongsTo
    {
        return $this->belongsTo(BrugaVeids::class, 'bruga_veids_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Atsauksme::class);
    }
}
