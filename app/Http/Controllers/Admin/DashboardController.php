<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use App\Models\Pegawai;

class DashboardController extends Controller
{
    public function index()
    {
        $pegawai = auth()->user()->pegawai;

        // Ambil ID tim admin
        $teamId = $pegawai->team_id;

        return view('admin.dashboard', [
            'totalTugas' => Tugas::whereHas('pegawai', fn($q) => $q->where('team_id', $teamId))->count(),
            'jumlahPegawai' => Pegawai::where('team_id', $teamId)->count(),
            'mostActive' => Pegawai::where('team_id', $teamId)
                            ->withCount('tugas')
                            ->orderByDesc('tugas_count')
                            ->first()
        ]);
    }
}
