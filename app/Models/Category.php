<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded(['id'])]
class Category extends Model
{
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

}
