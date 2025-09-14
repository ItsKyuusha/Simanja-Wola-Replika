<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Team;
use App\Models\Tugas;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        // ambil semua tim untuk header tabel
        $teams = Team::orderBy('nama_tim')->get();

        // ambil semua pegawai + relasi yang diperlukan
        $pegawais = Pegawai::with(['teams', 'tugas.jenisPekerjaan', 'tugas.semuaRealisasi'])->get();

        // kartu ringkasan
        $totalPegawai = Pegawai::count();

        $tugasQuery = Tugas::query();
        if ($bulan) $tugasQuery->whereMonth('created_at', $bulan);
        if ($tahun) $tugasQuery->whereYear('created_at', $tahun);

        $totalTugas = (clone $tugasQuery)->count();

        // ongoing = target belum tercapai
        $ongoing = (clone $tugasQuery)
            ->get()
            ->filter(fn($t) => $t->semuaRealisasi->sum('realisasi') < $t->target)
            ->count();

        // selesai = total realisasi >= target
        $selesai = (clone $tugasQuery)
            ->get()
            ->filter(fn($t) => $t->semuaRealisasi->sum('realisasi') >= $t->target)
            ->count();

        // nilai keseluruhan (persentase rata-rata)
        $allTugas = (clone $tugasQuery)->with('semuaRealisasi')->get();
        $nilaiKeseluruhan = 0;
        if ($allTugas->count() > 0) {
            $persenList = $allTugas->map(function ($t) {
                $totalRealisasi = $t->semuaRealisasi->sum('realisasi');
                return $t->target > 0 ? min($totalRealisasi / $t->target, 1) * 100 : 0;
            });
            $nilaiKeseluruhan = round($persenList->avg(), 2);
        }

        // mapping pegawai -> tim -> target & realisasi + score & grade
        $data = $pegawais->map(function ($pegawai) use ($teams, $bulan, $tahun) {
            $teamsData = $teams->map(function ($team) use ($pegawai, $bulan, $tahun) {
                $tugasQuery = $pegawai->tugas()->whereHas('jenisPekerjaan', function ($q) use ($team) {
                    $q->where('tim_id', $team->id);
                });

                if ($bulan) $tugasQuery->whereMonth('created_at', $bulan);
                if ($tahun) $tugasQuery->whereYear('created_at', $tahun);

                $tugasTim = $tugasQuery->with('semuaRealisasi')->get();
                $totalTarget = $tugasTim->sum('target');
                $totalRealisasi = $tugasTim
                    ->flatMap->semuaRealisasi
                    ->where('is_approved', true)
                    ->sum('realisasi');

                return [
                    'team_id'         => $team->id,
                    'nama_tim'        => $team->nama_tim ?? $team->nama ?? '—',
                    'total_target'    => $totalTarget,
                    'total_realisasi' => $totalRealisasi,
                ];
            });

            $grandTarget = $teamsData->sum('total_target');
            $grandRealisasi = $teamsData->sum('total_realisasi');

            // hitung score
            $score = $grandTarget > 0 ? round(($grandRealisasi / $grandTarget) * 100, 2) : 0;

            // tentukan grade
            if ($score >= 90) {
                $grade = 'SANGAT BAIK';
            } elseif ($score >= 80) {
                $grade = 'BAIK';
            } elseif ($score >= 70) {
                $grade = 'CUKUP';
            } elseif ($score >= 60) {
                $grade = 'SEDANG';
            } else {
                $grade = 'KURANG';
            }

            return [
                'pegawai'  => $pegawai,
                'teams'    => $teamsData,
                'score'    => $score,
                'grade'    => $grade,
                'grand_target' => $grandTarget,
                'grand_realisasi' => $grandRealisasi,
            ];
        });

        // siapkan data untuk chart
        $chartLabels = $data->pluck('pegawai.nama')->toArray();
        $chartTarget = $data->pluck('grand_target')->toArray();
        $chartRealisasi = $data->pluck('grand_realisasi')->toArray();

        return view('superadmin.dashboard', compact(
            'data',
            'teams',
            'totalPegawai',
            'totalTugas',
            'ongoing',
            'selesai',
            'nilaiKeseluruhan',
            'chartLabels',
            'chartTarget',
            'chartRealisasi'
        ));
    }
}
