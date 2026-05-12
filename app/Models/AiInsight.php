<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded(['id'])]
class AiInsight extends Model
{
    protected $casts = [
        'payload'      => 'array',
        'generated_at' => 'datetime',
    ];

    public function isStale(int $minutes = 10): bool
    {
        return $this->generated_at->lt(now()->subMinutes($minutes));
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
