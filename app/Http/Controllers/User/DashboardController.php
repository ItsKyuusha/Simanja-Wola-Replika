<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $pegawaiId = auth()->user()->pegawai_id;

        // Ambil nama tim user (asumsi kolom nama tim di tabel teams: nama_tim)
        $namaTim = DB::table('pegawai_team')
            ->join('teams', 'pegawai_team.team_id', '=', 'teams.id')
            ->where('pegawai_team.pegawai_id', $pegawaiId)
            ->value('teams.nama_tim'); // sesuaikan dengan kolom sebenarnya

        // Ambil semua tugas milik user sendiri
        $tugasSendiri = Tugas::with('jenisPekerjaan', 'semuaRealisasi')
            ->where('pegawai_id', $pegawaiId)
            ->get();

        // Hitung nilai akhir dan keterlambatan untuk setiap tugas
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

            // penalti 10% per hari keterlambatan
            $penalti = $bobot * 0.1 * $hariTelat;

            // nilai akhir
            $nilaiAkhir = max(0, ($bobot * $progress) - $penalti);

            return (object)[
                'tugas_id' => $t->id,
                'nama_pekerjaan' => $t->jenisPekerjaan->nama_pekerjaan ?? '-',
                'nama_tim' => $namaTim ?? '-',
                'target' => $t->target,
                'realisasi' => $totalRealisasi,
                'bobot' => $bobot,
                'hariTelat' => $hariTelat,
                'nilaiAkhir' => $nilaiAkhir,
            ];
        });

        // Statistik total
        $totalTugas = $tugasSendiri->count();
        $totalBobot = $tugasSendiri->sum(function ($t) {
            return $t->jenisPekerjaan->bobot ?? 0;
        });

        return view('user.dashboard', [
            'totalTugas' => $totalTugas,
            'totalBobot' => $totalBobot,
            'rincian' => $rincian,
        ]);
    }
}
