<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SiswaUserSeeder extends Seeder
{
    public function run()
    {
        $siswaData = [
            [
                'name' => 'Muhammad Afnan Alfian',
                'email' => 'siswa1@bimbel.com',
                'password' => Hash::make('password'),
                'province_id' => 73,
                'regency_id' => 7309,
                'phone' => '082154734819',
                'avatar' => null,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Nabila Iswara',
                'email' => 'siswa2@bimbel.com',
                'password' => Hash::make('password'),
                'province_id' => 73,
                'regency_id' => 7309,
                'phone' => '082169165041',
                'avatar' => null,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Muhammad Akram Almuafif',
                'email' => 'siswa3@bimbel.com',
                'password' => Hash::make('password'),
                'province_id' => 73,
                'regency_id' => 7309,
                'phone' => '085176803237',
                'avatar' => null,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($siswaData as $data) {
            $siswa = User::firstOrCreate(
                ['email' => $data['email']],
                $data
            );

            if (!$siswa->hasRole('siswa')) {
                $siswa->assignRole('siswa');
            }
        }

        echo "3 siswa users created/updated successfully.\n";
    }
}
