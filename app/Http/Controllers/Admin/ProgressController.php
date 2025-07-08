<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Tugas;
use Illuminate\Support\Facades\Log;

class ProgressController extends Controller
{
    public function index()
    {
        $teamId = auth()->user()->pegawai->team_id;
        
        // Ambil semua tugas di tim ini
        $tugas = Tugas::whereHas('pegawai', fn($q) => $q->where('team_id', $teamId))
                      ->with(['pegawai', 'realisasi'])
                      ->get();
Log::debug('Tugas:', ['tugas' => $tugas]);
        return view('admin.progress.index', compact('tugas'));
    }
}

