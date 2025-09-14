<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $pegawaiId = auth()->user()->pegawai_id;

        // Ambil nama tim user
        $namaTim = DB::table('pegawai_team')
            ->join('teams', 'pegawai_team.team_id', '=', 'teams.id')
            ->where('pegawai_team.pegawai_id', $pegawaiId)
            ->value('teams.nama_tim');

        // Ambil parameter filter
        $bulan  = $request->input('bulan');  // angka 1-12
        $tahun  = $request->input('tahun');  // angka 2020-2025
        $search = $request->input('search');

        // Query dasar
        $query = Tugas::with('jenisPekerjaan', 'semuaRealisasi')
            ->where('pegawai_id', $pegawaiId);

        if ($bulan) {
            $query->whereMonth('created_at', $bulan);
        }
        if ($tahun) {
            $query->whereYear('created_at', $tahun);
        }

        // 🔍 Filter search
        if ($search) {
            $query->whereHas('jenisPekerjaan', function ($q) use ($search) {
                $q->where('nama_pekerjaan', 'like', "%{$search}%");
            });
        }

        $tugasSendiri = $query->get();

        // Hitung rincian
        $rincian = $tugasSendiri->map(function ($t) use ($namaTim) {
            $totalRealisasi = $t->semuaRealisasi->sum('realisasi');
            $progress = $t->target > 0 ? min($totalRealisasi / $t->target, 1) : 0;

            $bobot = $t->jenisPekerjaan->bobot ?? 0;

            // keterlambatan
            $lastDate = $t->semuaRealisasi->max('tanggal_realisasi');
            $hariTelat = 0;
            if ($lastDate && Carbon::parse($lastDate)->gt(Carbon::parse($t->deadline))) {
                $hariTelat = Carbon::parse($lastDate)->diffInDays(Carbon::parse($t->deadline));
            }

            // penalti
            $penalti = $bobot * 0.1 * $hariTelat;
            $nilaiAkhir = max(0, ($bobot * $progress) - $penalti);

            return (object)[
                'tugas_id'       => $t->id,
                'nama_pekerjaan' => $t->jenisPekerjaan->nama_pekerjaan ?? '-',
                'nama_tim'       => $namaTim ?? '-',
                'target'         => $t->target,
                'realisasi'      => $totalRealisasi,
                'bobot'          => $bobot,
                'hariTelat'      => $hariTelat,
                'nilaiAkhir'     => $nilaiAkhir,
            ];
        });

        // Statistik total
        $totalTugas = $tugasSendiri->count();
        $totalBobot = $tugasSendiri->sum(fn ($t) => $t->jenisPekerjaan->bobot ?? 0);

        // 🔖 Label bulan & tahun (seperti superadmin)
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
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

        return view('user.dashboard', [
            'totalTugas'      => $totalTugas,
            'totalBobot'      => $totalBobot,
            'rincian'         => $rincian,
            'labelBulanTahun' => $labelBulanTahun,
        ]);
    }
}
