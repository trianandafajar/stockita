<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;

class DemoStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Stock::where('is_demo', true)->delete();

        $products = Product::where('is_demo', true)->get();
        $warehouses = Warehouse::where('is_demo', true)->get();

        // If you don't have data yet, stop the seeder to prevent errors
        if ($products->isEmpty() || $warehouses->isEmpty()) {
            return;
        }

        foreach ($products as $product) {
            foreach ($warehouses as $warehouse) {
                DB::table('stocks')->updateOrInsert(
                    [
                        'product_id'   => $product->id,
                        'warehouse_id' => $warehouse->id,
                    ],
                    [
                        'qty'          => rand(10, 100),
                        'is_demo'      => true,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]
                );
            }
        }
    }
}
