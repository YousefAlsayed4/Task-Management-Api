<?php

namespace Database\Seeders;

use App\Constants\Roles;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $managerRole = Role::firstOrCreate(['name' => Roles::MANAGER]);
        $userRole    = Role::firstOrCreate(['name' => Roles::USER]);

        // Managers
        $managers = [
            ['name' => 'Manager One', 'email' => 'manager1@test.com'],
            ['name' => 'Manager Two', 'email' => 'manager2@test.com'],
        ];

        foreach ($managers as $manager) {
            User::firstOrCreate(
                ['email' => $manager['email']],
                [
                    'name'     => $manager['name'],
                    'password' => Hash::make('password'),
                    'role_id'  => $managerRole->id,
                ]
            );
        }

        // Users
        $users = [
            ['name' => 'User One', 'email' => 'user1@test.com'],
            ['name' => 'User Two', 'email' => 'user2@test.com'],
            ['name' => 'User Three', 'email' => 'user3@test.com'],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                [
                    'name'     => $user['name'],
                    'password' => Hash::make('password'),
                    'role_id'  => $userRole->id,
                ]
            );
        }
    }
}
