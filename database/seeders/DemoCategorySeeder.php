<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Store;
use Illuminate\Database\Seeder;

class DemoCategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::where('is_demo', true)->delete();
        $storeId = Store::where('is_demo', true)->first()?->id;

        $categories = [
            ['name' => 'Food',           'slug' => 'food-products',           'is_demo' => true,  'store_id'    => $storeId,],
            ['name' => 'Beverages',      'slug' => 'beverage-products',       'is_demo' => true,  'store_id'    => $storeId,],
            ['name' => 'Snacks',         'slug' => 'snacks-and-appetizers',   'is_demo' => true,  'store_id'    => $storeId,],
            ['name' => 'Tobacco',        'slug' => 'tobacco-products',        'is_demo' => true,  'store_id'    => $storeId,],
            ['name' => 'Personal Care',  'slug' => 'personal-hygiene-products', 'is_demo' => true,  'store_id'    => $storeId,],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('Demo categories seeded.');
    }
}
