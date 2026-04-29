<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $permissions = [

            // Dashboard
            'view dashboard stats',

            // Products
            'view products',
            'create products',
            'edit products',
            'delete products',
            'upload product images',

            // Transactions
            'view transactions',
            'create transactions',
            'delete transactions',

            // Customers
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',
            'send customer email',

            // Categories
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',

            // Settings
            'view settings',
            'manage subscription',
            'edit own profile',

            // store
            'view store',
            'create store',
            'edit store',
            'delete store',

            // Inventory
            'view warehouse',
            'edit warehouse',
            'create warehouse',
            'delete warehouse',

            'create inventory',
            'delete inventory',
            'adjust stock',
            
            // role
            'edit roles',

            // Buyer
            'view own orders',
            'view order history',

            // Admin
            'manage roles & permissions',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
    }
}
