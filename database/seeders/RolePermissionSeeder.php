<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = [
            'superadmin',
            'guru',
            'siswa',
        ];
        foreach ($role as $item) {
            Role::create([
                'name' => $item,
                'guard_name' => 'web',
            ]);
        }

        $permission = [
            'manage-bimbingan-konseling',
        ];

        foreach ($permission as $item) {
            Permission::create([
                'name' => $item,
                'guard_name' => 'web',
            ]);
        }
    }
}
