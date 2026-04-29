<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'store_id',
    'user_id',
    'action',
    'model_type',
    'model_id',
    'metadata'
])]
class ActivityLog extends Model
{
    protected $casts = [
        'metadata' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
