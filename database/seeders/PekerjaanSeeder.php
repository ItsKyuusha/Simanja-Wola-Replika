<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pekerjaan;
use Carbon\Carbon;

class PekerjaanSeeder extends Seeder
{
    public function run(): void
    {
        Pekerjaan::insert([
            [
                'user_id' => 2,
                'tugas' => 'Rekap Data Industri Mikro',
                'bobot' => 30.00,
                'asal' => 'Internal',
                'target' => 20,
                'realisasi' => 18,
                'satuan' => 'laporan',
                'deadline' => Carbon::now()->addDays(7),
                'catatan' => 'Kurang 2 data',
                'tanggal_realisasi' => Carbon::now(),
                'file' => null,
                'nilai_kualitas' => 80,
                'nilai_kuantitas' => 90,
                'keterangan' => 'Progres Baik',
            ],
        ]);
    }
}

