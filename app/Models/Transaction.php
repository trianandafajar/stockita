<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded(['id'])]
class Transaction extends Model
{
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
