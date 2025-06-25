<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisTim;

class JenisTimSeeder extends Seeder
{
    public function run(): void
    {
        JenisTim::insert([
            ['nama' => 'Statistik Sosial'],
            ['nama' => 'Statistik Produksi'],
            ['nama' => 'Distribusi dan Jasa'],
        ]);
    }
}

