<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded(['id'])]
class Product extends Model
{
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
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
