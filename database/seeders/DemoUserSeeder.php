<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Admin Demo',
                'email'    => 'admin@demo.com',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'is_demo'  => true,
            ],
            [
                'name'     => 'Owner Demo',
                'email'    => 'owner@demo.com',
                'password' => Hash::make('password'),
                'role'     => 'owner',
                'is_demo'  => true,
            ],
            [
                'name'     => 'Buyer Demo',
                'email'    => 'buyer@demo.com',
                'password' => Hash::make('password'),
                'role'     => 'buyer',
                'is_demo'  => true,
            ],
        ];

        foreach ($users as $user) {
            $user_role = User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => $user['password'],
                    'is_demo' => $user['is_demo']
                ]
            );

            $user_role->syncRoles([$user['role']]);
        }

        $this->command->info('Demo users seeded.');
    }
}
