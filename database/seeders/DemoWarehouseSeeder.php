<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Store;
use App\Models\Warehouse;

class DemoWarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Warehouse::where('is_demo', true)->delete();

        $storeId = Store::where('is_demo', true)->first()?->id;

        $warehouses = [
            [
                'name'        => 'Main Central Warehouse',
                'code'        => 'WH-CENTRAL-01',
                'store_id'    => $storeId,
                'location'    => 'Chicago, Illinois',
                'description' => 'Primary distribution hub for all inventory.',
                'is_active'   => true,
            ],
            [
                'name'        => 'North Side Branch Storage',
                'code'        => 'WH-NORTH-02',
                'store_id'    => $storeId,
                'location'    => 'Evanston, Illinois',
                'description' => 'Secondary storage for fast-moving consumer goods.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Online Fulfillment Center',
                'code'        => 'WH-ECOM-03',
                'store_id'    => $storeId,
                'location'    => 'Digital Park, Sector 7',
                'description' => 'Dedicated warehouse for e-commerce orders.',
                'is_active'   => true,
            ],
        ];

        foreach ($warehouses as $warehouse) {
            DB::table('warehouses')->insert(array_merge($warehouse, [
                'is_demo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
