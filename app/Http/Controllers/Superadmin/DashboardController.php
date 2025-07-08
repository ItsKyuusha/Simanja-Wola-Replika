<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Team;
use App\Models\Tugas;
use App\Models\Progress;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Data ringkas
        $totalProject = Tugas::count();
        $totalTeam = Team::count();
        $totalPegawai = Pegawai::count();

        // Most active (pegawai dengan jumlah tugas terbanyak)
        $mostActive = Pegawai::withCount('tugas')
            ->orderByDesc('tugas_count')
            ->first();

        // 1. Jumlah kegiatan (jumlah tugas per pegawai)
        $jumlahKegiatan = Pegawai::withCount('tugas')
            ->orderByDesc('tugas_count')
            ->get(['id', 'nama']);

        // 2. Total bobot pekerjaan (dari relasi ke jenis_pekerjaan)
        $bobotPerPegawai = Pegawai::select('pegawais.id', 'pegawais.nama', DB::raw('COALESCE(SUM(jp.bobot),0) as total_bobot'))
            ->leftJoin('tugas as t', 'pegawais.id', '=', 't.pegawai_id')
            ->leftJoin('jenis_pekerjaans as jp', 't.jenis_pekerjaan_id', '=', 'jp.id')
            ->groupBy('pegawais.id', 'pegawais.nama')
            ->orderByDesc('total_bobot')
            ->get();

        // 3. Nilai akhir pegawai (dari tabel progress)
        $nilaiKinerja = Progress::with('pegawai')
            ->orderByDesc('nilai_akhir')
            ->get();

        // 4. Persentase tugas selesai = jumlah tugas dengan realisasi / total tugas
        $persentaseSelesai = Pegawai::select('pegawais.id', 'pegawais.nama',
            DB::raw('COUNT(t.id) as total_tugas'),
            DB::raw('COUNT(rt.id) as tugas_selesai'),
            DB::raw('ROUND(COUNT(rt.id) / NULLIF(COUNT(t.id), 0) * 100, 2) as persen_selesai')
        )
            ->leftJoin('tugas as t', 'pegawais.id', '=', 't.pegawai_id')
            ->leftJoin('realisasi_tugas as rt', 't.id', '=', 'rt.tugas_id')
            ->groupBy('pegawais.id', 'pegawais.nama')
            ->orderByDesc('persen_selesai')
            ->get();

        return view('superadmin.dashboard', compact(
            'totalProject',
            'totalTeam',
            'totalPegawai',
            'mostActive',
            'jumlahKegiatan',
            'bobotPerPegawai',
            'nilaiKinerja',
            'persentaseSelesai'
        ));
    }
}
