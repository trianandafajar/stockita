<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // reset cache role & permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // buat role
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'owner']);
        Role::firstOrCreate(['name' => 'buyer']);
    }
}
