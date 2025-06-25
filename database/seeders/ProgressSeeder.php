<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Progress;
use Carbon\Carbon;

class ProgressSeeder extends Seeder
{
    public function run(): void
    {
        Progress::insert([
            [
                'user_id' => 2,
                'tugas' => 'Monitoring Survei',
                'bobot' => 20.00,
                'asal' => 'Pusat',
                'target' => 100,
                'realisasi' => 90,
                'satuan' => 'responden',
                'deadline' => Carbon::now()->addDays(10),
                'tanggal_realisasi' => Carbon::now(),
                'nilai_kualitas' => 85,
                'nilai_kuantitas' => 88,
                'keterangan' => 'Tercapai',
            ],
        ]);
    }
}

