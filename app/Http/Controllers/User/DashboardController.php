<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use App\Models\RealisasiTugas;

class DashboardController extends Controller
{
    public function index()
    {
        $pegawaiId = auth()->user()->pegawai_id;

        return view('user.dashboard', [
            'totalTugas' => Tugas::where('pegawai_id', $pegawaiId)->count(),
            'totalRealisasi' => RealisasiTugas::whereHas('tugas', fn($q) =>
                $q->where('pegawai_id', $pegawaiId)
            )->count(),
        ]);
    }
}
