<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionAssignSeeder extends Seeder
{
    public function run()
    {
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $owner = Role::firstOrCreate(['name' => 'owner']);
        $buyer = Role::firstOrCreate(['name' => 'buyer']);

        $admin->syncPermissions(Permission::all());

        $owner->syncPermissions([
            // Dashboard
            'view dashboard stats',

            // Categories
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',

            // Products
            'view products',
            'create products',
            'edit products',
            'delete products',
            'upload product images',

            // Invento
            'view warehouse',
            'edit warehouse',
            'create warehouse',
            'delete warehouse',

            'create inventory',             
            'delete inventory',
            'adjust stock',

            // Customers
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',
            'send customer email',

            // Transactions
            'view transactions',
            'create transactions',
            'delete transactions',

            // Settings
            'view settings',
            'edit store',
            'edit own profile'
        ]);

        $buyer->syncPermissions([
            'view own orders',
            'view order history',
            'edit own profile',
        ]);
    }
}
