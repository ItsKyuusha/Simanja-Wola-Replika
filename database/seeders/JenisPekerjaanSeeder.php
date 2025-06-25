<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisPekerjaan;

class JenisPekerjaanSeeder extends Seeder
{
    public function run(): void
    {
        JenisPekerjaan::insert([
            [
                'nama' => 'Survei Sosial Ekonomi Nasional',
                'satuan' => 'kegiatan',
                'bobot' => 25.00,
                'pemberi_pekerjaan' => 'Kepala Seksi Sosial',
            ],
            [
                'nama' => 'Survei Industri Mikro',
                'satuan' => 'laporan',
                'bobot' => 30.00,
                'pemberi_pekerjaan' => 'Kepala Seksi Produksi',
            ],
        ]);
    }
}

