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
        $bulan = request('bulan') ?: null;
        $tahun = request('tahun') ?: null;

        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        if ($bulan && $tahun) {
            $labelBulanTahun = strtoupper($namaBulan[(int)$bulan]) . ' ' . $tahun;
        } elseif ($bulan && !$tahun) {
            $labelBulanTahun = strtoupper($namaBulan[(int)$bulan]) . ' - Semua Tahun';
        } elseif (!$bulan && $tahun) {
            $labelBulanTahun = 'Semua Bulan - ' . $tahun;
        } else {
            $labelBulanTahun = 'Semua Bulan & Tahun';
        }

        $search = trim((string) request('search', ''));
        $keywords = $search === ''
            ? []
            : array_values(array_filter(array_map('trim', explode(',', $search))));

        $totalProject = Tugas::when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
            ->count();

        $totalTeam = Team::count();
        $totalPegawai = Pegawai::count();

        $mostActive = Pegawai::withCount(['tugas' => function ($query) use ($bulan, $tahun) {
            $query->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
                ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun));
        }])
            ->orderByDesc('tugas_count')
            ->first();

        $jumlahKegiatan = Pegawai::select('id', 'nama')
            ->when($keywords, function ($query) use ($keywords) {
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $word) {
                        $q->orWhere('nama', 'like', '%' . $word . '%');
                    }
                });
            })
            ->withCount(['tugas' => function ($query) use ($bulan, $tahun) {
                $query->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
                    ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun));
            }])
            ->orderByDesc('tugas_count')
            ->get();

        // HAPUS bobotPerPegawai

        $nilaiKinerja = Progress::with('pegawai')
            ->whereHas('pegawai', function ($q) use ($keywords) {
                if (!empty($keywords)) {
                    $q->where(function ($sub) use ($keywords) {
                        foreach ($keywords as $word) {
                            $sub->orWhere('nama', 'like', "%{$word}%");
                        }
                    });
                }
            })
            ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
            ->orderByDesc('nilai_akhir')
            ->get();

        $persentaseSelesai = Pegawai::select(
            'pegawais.id',
            'pegawais.nama',
            DB::raw('COUNT(t.id) as total_tugas'),
            DB::raw('COUNT(rt.id) as tugas_selesai'),
            DB::raw('ROUND(COUNT(rt.id) / NULLIF(COUNT(t.id), 0) * 100, 2) as persen_selesai')
        )
            ->when(!empty($keywords), function ($query) use ($keywords) {
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $word) {
                        $q->orWhere('pegawais.nama', 'like', "%{$word}%");
                    }
                });
            })
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
            'nilaiKinerja',
            'persentaseSelesai',
            'labelBulanTahun',
        ));
    }
}
