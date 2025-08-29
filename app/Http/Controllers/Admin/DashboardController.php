<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Tugas;
use App\Models\Pegawai;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $pegawai = auth()->user()->pegawai;
        $teamId = $pegawai->team_id;

        // Ambil filter dari request
        $bulan = $request->bulan ?: null;
        $tahun = $request->tahun ?: null;

        // Array nama bulan dalam Bahasa Indonesia
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Label tampilan untuk Bulan dan Tahun
        if ($bulan && $tahun) {
            $labelBulanTahun = strtoupper($namaBulan[(int)$bulan]) . ' ' . $tahun;
        } elseif ($bulan && !$tahun) {
            $labelBulanTahun = strtoupper($namaBulan[(int)$bulan]) . ' - Semua Tahun';
        } elseif (!$bulan && $tahun) {
            $labelBulanTahun = 'Semua Bulan - ' . $tahun;
        } else {
            $labelBulanTahun = 'Semua Bulan & Tahun';
        }

        // Total Tugas untuk tim ini
        $totalTugas = Tugas::whereHas('pegawai', fn($q) => $q->where('team_id', $teamId))
            ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
            ->count();

        // Jumlah pegawai dalam tim
        $jumlahPegawai = Pegawai::where('team_id', $teamId)->count();

        // Pegawai teraktif dalam tim ini
        $mostActive = Pegawai::where('team_id', $teamId)
            ->withCount(['tugas' => function ($query) use ($bulan, $tahun) {
                $query->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
                      ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun));
            }])
            ->orderByDesc('tugas_count')
            ->first();

        // Tabel Grafik Jumlah kegiatan per pegawai (untuk tab "kegiatan")
        $jumlahKegiatan = Pegawai::where('team_id', $teamId)
            ->withCount(['tugas' => function ($query) use ($bulan, $tahun) {
                $query->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
                    ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun));
            }])
            ->get();
        
        
        // Total Bobot per Pegawai (untuk tab "bobot")
        $bobotPerPegawai = Pegawai::where('team_id', $teamId)
            ->withSum(['tugas as total_bobot' => function ($query) use ($bulan, $tahun) {
                $query->select(\DB::raw('COALESCE(SUM(jenis_pekerjaans.bobot), 0)'))
              ->join('jenis_pekerjaans', 'tugas.jenis_pekerjaan_id', '=', 'jenis_pekerjaans.id')
              ->when($bulan, fn($q) => $q->whereMonth('tugas.created_at', $bulan))
              ->when($tahun, fn($q) => $q->whereYear('tugas.created_at', $tahun));
        }], 'bobot')
            ->orderByDesc('total_bobot')
            ->get();

        // Nilai Kinerja Pegawai (khusus anggota tim admin)
        $nilaiKinerja = Progress::with('pegawai')
            ->whereHas('pegawai', fn($q) => $q->where('team_id', $teamId)) // ⬅️ filter tim admin
            ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
            ->orderByDesc('nilai_akhir')
             ->get();

        // Persentase Tugas Selesai (khusus anggota tim admin)
        $persentaseSelesai = Pegawai::select(
                'pegawais.id', 'pegawais.nama',
                DB::raw('COUNT(t.id) as total_tugas'),
                DB::raw('COUNT(rt.id) as tugas_selesai'),
                DB::raw('ROUND(COUNT(rt.id) / NULLIF(COUNT(t.id), 0) * 100, 2) as persen_selesai')
            )
            ->leftJoin('tugas as t', 'pegawais.id', '=', 't.pegawai_id')
            ->leftJoin('realisasi_tugas as rt', 't.id', '=', 'rt.tugas_id')
            ->when($bulan, fn($q) => $q->whereMonth('t.created_at', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('t.created_at', $tahun))
            ->where('pegawais.team_id', $teamId) // ⬅️ filter tim admin
            ->groupBy('pegawais.id', 'pegawais.nama')
            ->orderByDesc('persen_selesai')
            ->get();

        return view('admin.dashboard', compact(
            'totalTugas',
            'jumlahPegawai',
            'mostActive',
            'labelBulanTahun',
            'jumlahKegiatan',
            'bobotPerPegawai',
            'nilaiKinerja',
            'persentaseSelesai',
        ));
    }
}
