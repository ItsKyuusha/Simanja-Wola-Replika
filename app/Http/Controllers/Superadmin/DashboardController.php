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
        // Ambil filter dari request
        $bulan = request('bulan') ?: null;
        $tahun = request('tahun') ?: null;

        // Array nama bulan dalam Bahasa Indonesia
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Label tampilan untuk Bulan dan Tahun
        $labelBulanTahun = null;
        if ($bulan && $tahun) {
            $labelBulanTahun = strtoupper($namaBulan[(int)$bulan]) . ' ' . $tahun;
        } elseif ($bulan && !$tahun) {
            $labelBulanTahun = strtoupper($namaBulan[(int)$bulan]) . ' - Semua Tahun';
        } elseif (!$bulan && $tahun) {
            $labelBulanTahun = 'Semua Bulan - ' . $tahun;
        } else {
            $labelBulanTahun = 'Semua Bulan & Tahun';
        }

        // Data ringkasan
        $totalProject = Tugas::when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
                             ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
                             ->count();

        $totalTeam = Team::count();
        $totalPegawai = Pegawai::count();

        // Most Active Pegawai
        $mostActive = Pegawai::withCount(['tugas' => function ($query) use ($bulan, $tahun) {
                $query->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
                      ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun));
            }])
            ->orderByDesc('tugas_count')
            ->first();

        // Jumlah Kegiatan Pegawai
        $search = request('search');

        $jumlahKegiatan = Pegawai::select('id', 'nama')
            ->when($search, function ($query) use ($search) {
                $query->where('nama', 'like', '%' . $search . '%');
            })
            ->withCount(['tugas' => function ($query) use ($bulan, $tahun) {
                $query->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
                      ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun));
            }])
            ->orderByDesc('tugas_count')
            ->get();

        // Total Bobot per Pegawai
        $bobotPerPegawai = Pegawai::select('pegawais.id', 'pegawais.nama', DB::raw('COALESCE(SUM(jp.bobot),0) as total_bobot'))
            ->leftJoin('tugas as t', 'pegawais.id', '=', 't.pegawai_id')
            ->leftJoin('jenis_pekerjaans as jp', 't.jenis_pekerjaan_id', '=', 'jp.id')
            ->when($bulan, fn($q) => $q->whereMonth('t.created_at', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('t.created_at', $tahun))
            ->groupBy('pegawais.id', 'pegawais.nama')
            ->orderByDesc('total_bobot')
            ->get();

        // Nilai Kinerja Pegawai
        $nilaiKinerja = Progress::with('pegawai')
            ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
            ->orderByDesc('nilai_akhir')
            ->get();

        // Persentase Tugas Selesai
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
            'persentaseSelesai',
            'labelBulanTahun',
        ));
    }
}