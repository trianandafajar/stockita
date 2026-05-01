<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Store;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = User::where('email', 'owner@demo.com')->first();

        $store =  Store::firstOrCreate([
            'name' => 'Store Demo',
            'slug' => 'store-demo',
            'email' => 'store@demo.com',
            'phone' => '08123456789',
            'owner_id' => $owner->id,
            'is_demo' => true,
            'address' => '45 Industrial Way, Manchester, M1 4BT, United Kingdom',
        ]);

        $buyer = User::where('email', 'buyer@demo.com')->first();

        Customer::firstOrCreate([
            'user_id' => $buyer->id,
            'phone' => '+13059565677',
            'store_id' => $store->id,
            'is_demo' => true
        ]);

        Subscription::firstOrCreate([
            'user_id' => $owner->id,
            'plan_id' => 3,
            'interval' => 'monthly',
            'status' => 'active',
            'current_period_end' => now()->addMonth(),
            'is_demo' => true
        ]);
    }
}
