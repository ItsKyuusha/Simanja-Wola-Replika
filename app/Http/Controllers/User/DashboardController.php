<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use App\Models\RealisasiTugas;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $pegawaiId = auth()->user()->pegawai_id;

        // 1) ringkasan untuk user login
        $totalTugas = Tugas::where('pegawai_id', $pegawaiId)->count();
        $totalRealisasi = RealisasiTugas::whereHas('tugas', function ($q) use ($pegawaiId) {
            $q->where('pegawai_id', $pegawaiId);
        })->count();

        // 2) total target per pegawai (dari tabel tugas)
        $targets = DB::table('tugas')
            ->select('pegawai_id', DB::raw('SUM(target) as total_target'))
            ->groupBy('pegawai_id')
            ->get()
            ->keyBy('pegawai_id');

        // 3) total realisasi per pegawai (join realisasi -> tugas)
        $reals = DB::table('realisasi_tugas')
            ->join('tugas', 'realisasi_tugas.tugas_id', '=', 'tugas.id')
            ->select('tugas.pegawai_id as pegawai_id', DB::raw('SUM(realisasi_tugas.realisasi) as total_realisasi'))
            ->groupBy('tugas.pegawai_id')
            ->get()
            ->keyBy('pegawai_id');

        // 4) rata-rata nilai kualitas/kuantitas per pegawai (jika ada)
        $avgScores = DB::table('realisasi_tugas')
            ->join('tugas', 'realisasi_tugas.tugas_id', '=', 'tugas.id')
            ->select(
                'tugas.pegawai_id as pegawai_id',
                DB::raw('AVG(realisasi_tugas.nilai_kualitas) as avg_kualitas'),
                DB::raw('AVG(realisasi_tugas.nilai_kuantitas) as avg_kuantitas')
            )
            ->groupBy('tugas.pegawai_id')
            ->get()
            ->keyBy('pegawai_id');

        // 5) gabungkan daftar pegawai yang punya target / realisasi / avg
        $pegawaiIds = collect(array_unique(array_merge(
            $targets->keys()->toArray(),
            $reals->keys()->toArray(),
            $avgScores->keys()->toArray()
        )));

        // no data -> kirim view kosong
        if ($pegawaiIds->isEmpty()) {
            return view('user.dashboard', [
                'totalTugas' => $totalTugas,
                'totalRealisasi' => $totalRealisasi,
                'palingAktif' => null,
                'rincian' => collect(),
                'chartData' => [],
            ]);
        }

        // 6) ambil nama pegawai
        $pegawais = DB::table('pegawais')
            ->whereIn('id', $pegawaiIds->toArray())
            ->select('id', 'nama')
            ->get()
            ->keyBy('id');

        // 7) bangun rows: target, realisasi, capaian, kuantitas, kualitas
        $rows = collect();
        foreach ($pegawaiIds as $id) {
            $nama = $pegawais[$id]->nama ?? ('ID#' . $id);
            $total_target = isset($targets[$id]) ? (float) $targets[$id]->total_target : 0.0;
            $total_realisasi = isset($reals[$id]) ? (float) $reals[$id]->total_realisasi : 0.0;

            // capaian persen terhadap target
            $capaian = $total_target > 0 ? round(($total_realisasi / $total_target) * 100, 2) : 0.0;

            // kuantitas := persen realisasi terhadap target (=> sama dengan capaian)
            $kuantitas = $capaian;

            // kualitas := rata-rata nilai_kualitas jika ada, else fallback ke kuantitas
            $avg_kualitas = isset($avgScores[$id]) && $avgScores[$id]->avg_kualitas !== null
                ? round((float)$avgScores[$id]->avg_kualitas, 2)
                : null;

            $kualitas = $avg_kualitas !== null ? $avg_kualitas : $kuantitas;

            $rows->push((object)[
                'pegawai_id' => $id,
                'nama' => $nama,
                'total_target' => $total_target,
                'total_realisasi' => $total_realisasi,
                'capaian' => $capaian,
                'kuantitas' => $kuantitas,
                'kualitas' => $kualitas,
            ]);
        }

        // 8) hitung skor (bobot) dan grade
        $rows = $rows->map(function ($r) {
            // bobot: 60% kuantitas, 40% kualitas (ubah jika perlu)
            $r->skor = round((0.6 * $r->kuantitas) + (0.4 * $r->kualitas), 2);

            // grade contoh berdasarkan capaian
            if ($r->capaian >= 90) {
                $r->grade = 'Sangat Baik';
            } elseif ($r->capaian >= 75) {
                $r->grade = 'Baik';
            } elseif ($r->capaian >= 50) {
                $r->grade = 'Cukup';
            } else {
                $r->grade = 'Kurang';
            }

            return $r;
        });

        // 9) tentukan paling aktif berdasarkan skor tertinggi
        $palingAktifObj = $rows->sortByDesc('skor')->first();
        $palingAktif = $palingAktifObj ? [
            'nama' => $palingAktifObj->nama,
            'total_realisasi' => $palingAktifObj->total_realisasi,
            'total_target' => $palingAktifObj->total_target,
            'capaian' => $palingAktifObj->capaian,
            'kuantitas' => $palingAktifObj->kuantitas,
            'kualitas' => $palingAktifObj->kualitas,
            'skor' => $palingAktifObj->skor,
        ] : null;

        // 10) siapkan chartData (nama, target, realisasi)
        $chartData = $rows->map(function ($r) {
            return [
                'nama' => $r->nama,
                'target' => (float) $r->total_target,
                'realisasi' => (float) $r->total_realisasi,
                'kuantitas' => $r->kuantitas,
                'kualitas' => $r->kualitas,
                'skor' => $r->skor,
            ];
        })->values()->toArray();

        // 11) kirim ke view
        $rincian = $rows->sortByDesc('skor')->values();

        return view('user.dashboard', [
            'totalTugas' => $totalTugas,
            'totalRealisasi' => $totalRealisasi,
            'palingAktif' => $palingAktif,
            'rincian' => $rincian,
            'chartData' => $chartData,
        ]);
    }
}
