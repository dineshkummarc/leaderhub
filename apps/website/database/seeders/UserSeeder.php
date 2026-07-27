<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Leaderhub\Core\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super_admin',
            'content_admin',
            'sales_admin',
            'inventory_admin',
            'accounting_admin',
            'hr_admin',
            'company_admin',
            'license_admin',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@leaderhub.id'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $superAdmin->assignRole('super_admin');

        $contentAdmin = User::firstOrCreate(
            ['email' => 'content@leaderhub.id'],
            [
                'name' => 'Content Admin',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $contentAdmin->assignRole('content_admin');
    }
}
