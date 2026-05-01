<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            DemoUserSeeder::class,
            DemoStoreSeeder::class,
            DemoWarehouseSeeder::class,
            DemoCategorySeeder::class,
            DemoProductSeeder::class,
            DemoStockSeeder::class,
            DemoTransactionSeeder::class,
        ]);
    }
}
