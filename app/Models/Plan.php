<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded(['id'])]
class Plan extends Model
{
    protected $casts = [
        'features' => 'array',
    ];

    public function subscription()
    {
        return $this->hasMany(Subscription::class);
    }

    public function midtransTransaction()
    {
        return $this->hasMany(MidtransTransaction::class);
    }
}
