<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Constants\Roles;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run()
    {
        Role::firstOrCreate(['name' => Roles::MANAGER]);
        Role::firstOrCreate(['name' => Roles::USER]);
    }
}
