<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::insert([
            [
                'nama' => 'Super Admin',
                'nip' => '00000001',
                'tim_id' => 1,
                'jabatan' => 'Kepala BPS',
                'email' => 'superadmin@bps.go.id',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
            ],
            [
                'nama' => 'Admin Produksi',
                'nip' => '00000002',
                'tim_id' => 2,
                'jabatan' => 'Statistisi Ahli Muda',
                'email' => 'admin@bps.go.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'nama' => 'Siti Lestari',
                'nip' => '00000003',
                'tim_id' => 3,
                'jabatan' => 'Fungsional Statistik',
                'email' => 'siti@bps.go.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
        ]);
    }
}
