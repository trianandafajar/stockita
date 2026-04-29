<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Plan::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Plan::create([
            'name' => 'Starter',
            'price' => 0,
            'max_products' => 10,
            'max_orders' => 50,
            'max_customers' => 5,
            'max_categories' => 100,
            'max_warehouses' => 5,
            'duration_days' => 30,
            'features' => [
                'Kelola produk',
                '10 produk',
                '5 Gudang',
            ],
        ]);

        Plan::create([
            'name' => 'Pro',
            'price' => 99000,
            'yearly_price' => 990000,
            'max_products' => 100,
            'max_customers' => 100,
            'max_categories' => 500,
            'max_warehouses' => 50,
            'max_orders' => 500,
            'duration_days' => 30,
            'features' => [
                'Semua fitur Starter',
                '100 produk',
                '50 Gudang',
            ],
        ]);

        Plan::create([
            'name' => 'Business',
            'price' => 199000,
            'yearly_price' => 1990000,
            'max_products' => null,
            'max_orders' => null,
            'max_customers' => null,
            'max_categories' => null,
            'max_warehouses' => null,
            'duration_days' => 30,
            'features' => [
                'Semua fitur Pro',
                'Unlimited produk',
                'Unlimited Gudang',
            ],
        ]);
    }
}
