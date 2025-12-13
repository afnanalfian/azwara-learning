<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            'admin',
            'tentor',
            'siswa',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName,'guard_name' => 'web']);
        }

        echo "Roles (admin, tentor, siswa) created/verified successfully.\n";
    }
}
