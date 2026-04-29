<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded(['id'])]
class Warehouse extends Model
{
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
