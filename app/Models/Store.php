<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded(['id'])]
class Store extends Model
{

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function warehouse()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

}
