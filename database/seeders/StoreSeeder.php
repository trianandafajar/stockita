<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = User::where('email', 'owner@gmail.com')->first();

        $store =  Store::firstOrCreate([
            'name' => 'Stockita',
            'slug' => 'stoc-kita',
            'email' => 'StocKita@email.com',
            'phone' => '08123456789',
            'owner_id' => $owner->id,
            'address' => 'Jl. Pandanaran No. 123, Semarang, Jawa Tengah, 50241, Indonesia',
        ]);

        $buyer = User::where('email', 'buyer@gmail.com')->first();

        Customer::firstOrCreate([
            'user_id' => $buyer->id,
            'phone' => '+13059565677',
            'store_id' => $store->id,
        ]);
    }
}
