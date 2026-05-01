<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\Warehouse;
use App\Models\User;

class DemoProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::where('is_demo', true)->delete();

        // Get IDs from related tables
        $foodId         = Category::where('slug', 'food-products')->where('is_demo', true)->first()?->id;
        $beverageId     = Category::where('slug', 'beverage-products')->where('is_demo', true)->first()?->id;
        $snackId        = Category::where('slug', 'snacks-and-appetizers')->where('is_demo', true)->first()?->id;
        $personalCareId = Category::where('slug', 'personal-hygiene-products')->where('is_demo', true)->first()?->id;

        $warehouseId    = Warehouse::where('is_demo', true)->first()?->id;
        $userId         = User::where('is_demo', true)->first()?->id;
        $storeId        = Store::where('is_demo', true)->first()?->id;

        $products = [
            [
                'name'         => 'Whole Grain Bread',
                'sku'          => 'FOD-WGB-001',
                'price'        => 25000,
                'category_id'  => $foodId,
                'warehouse_id' => $warehouseId,
                'created_by'   => $userId,
                'store_id'     => $storeId,
            ],
            [
                'name'         => 'Mineral Water 500ml',
                'sku'          => 'BEV-MW-002',
                'price'        => 5000,
                'category_id'  => $beverageId,
                'warehouse_id' => $warehouseId,
                'created_by'   => $userId,
                'store_id'     => $storeId,
            ],
            [
                'name'         => 'Potato Chips Salted',
                'sku'          => 'SNK-PCS-003',
                'price'        => 15000,
                'category_id'  => $snackId,
                'warehouse_id' => $warehouseId,
                'created_by'   => $userId,
                'store_id'     => $storeId,
            ],
            [
                'name'         => 'Antiseptic Hand Soap',
                'sku'          => 'PC-AHS-004',
                'price'        => 35000,
                'category_id'  => $personalCareId,
                'warehouse_id' => $warehouseId,
                'created_by'   => $userId,
                'store_id'     => $storeId,
            ],
            [
                'name'         => 'Instant Oatmeal Cup',
                'sku'          => 'FOD-IOC-005',
                'price'        => 12500,
                'category_id'  => $foodId,
                'warehouse_id' => $warehouseId,
                'created_by'   => $userId,
                'store_id'     => $storeId,
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert(array_merge($product, [
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
                'is_demo'      => true
            ]));
        }

        $this->command->info('Demo Products seeded.');
    }
}
