<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Team;
use App\Models\JenisTim;
use App\Models\JenisPekerjaan;
use App\Models\Tugas;

class WolaSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Helmi Azkia',
            'email' => 'superadmin@bps.go.id',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'pegawai_id' => null
        ]);
    }
}
