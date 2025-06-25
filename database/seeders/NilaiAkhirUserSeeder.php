<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NilaiAkhirUser;

class NilaiAkhirUserSeeder extends Seeder
{
    public function run(): void
    {
        NilaiAkhirUser::insert([
            [
                'user_id' => 2,
                'kategori_bobot' => 'Kuantitatif',
                'total_bobot' => 50.00,
                'nilai_akhir' => 87.50,
            ],
        ]);
    }
}
