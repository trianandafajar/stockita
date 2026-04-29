<?php

namespace App\Observers;

use App\Mail\OutOfStockMail;
use App\Models\Stock;
use App\Models\User;
use App\Notifications\StockLowNotification;
use App\Notifications\StockOutNotification;
use Illuminate\Support\Facades\Mail;

class StockObserver
{
    /**
     * Handle the Stock "created" event.
     */
    public function created(Stock $stock): void
    {
        //
    }

    /**
     * Handle the Stock "updated" event.
     */


    public function updated(Stock $stock)
    {
        // owner
        $owner = $stock->warehouse->store->owner;

        // admin
        $admins = User::role('admin')->get();

        // stok habis
        if ($stock->qty == 0 && $stock->getOriginal('qty') != 0) {

            // sesuai toko
            if ($owner) {
                $owner->notify(new StockOutNotification($stock));

                Mail::to($owner->email)->send(
                    new OutOfStockMail([
                        'name' => $owner->name,
                        'product_name' => $stock->product->name,
                        'product_code' => $stock->product->sku,
                        'store_name' => $owner->store->name,
                        'warehouse_id' => $stock->warehouse_id
                    ])
                );
            }

            // notify admin
            foreach ($admins as $admin) {
                $admin->notify(new StockOutNotification($stock));
            }
        }

        // stok menipis
        if ($stock->qty > 0 && $stock->qty <= 5 && $stock->getOriginal('qty') > 5) {

            if ($owner) {
                $owner->notify(new StockLowNotification($stock));
            }

            foreach ($admins as $admin) {
                $admin->notify(new StockLowNotification($stock));
            }
        }
    }

    /**
     * Handle the Stock "deleted" event.
     */
    public function deleted(Stock $stock): void
    {
        //
    }

    /**
     * Handle the Stock "restored" event.
     */
    public function restored(Stock $stock): void
    {
        //
    }

    /**
     * Handle the Stock "force deleted" event.
     */
    public function forceDeleted(Stock $stock): void
    {
        //
    }
}
