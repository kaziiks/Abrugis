<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    protected $table = 'applications';

    protected $casts = [
        'requested_date' => 'date',
        'estimate_details' => 'array',
    ];

    protected $fillable = [
        'user_id',
        'paving_type_id',
        'client_name',
        'client_email',
        'client_phone',
        'project_description',
        'area_m2',
        'requested_date',
        'estimate_total',
        'estimate_details',
        'status',
        'admin_notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pavingType(): BelongsTo
    {
        return $this->belongsTo(PavingType::class, 'paving_type_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
