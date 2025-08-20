<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Progress;
use App\Models\Tugas;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    public function index()
    {
        // Hitung dan simpan progress tiap pegawai
        $pegawais = Pegawai::with(['tugas.realisasi', 'tugas.jenisPekerjaan'])->get();

        foreach ($pegawais as $pegawai) {
            $totalBobot = 0;
            $totalNilai = 0;
            $jumlahTugas = 0;

            foreach ($pegawai->tugas as $tugas) {
                if ($tugas->realisasi) {
                    $bobot = $tugas->jenisPekerjaan->bobot ?? 0;
                    $kualitas = $tugas->realisasi->nilai_kualitas ?? 0;
                    $kuantitas = $tugas->realisasi->nilai_kuantitas ?? 0;
                    $nilai = ($kualitas + $kuantitas) / 2;

                    $totalBobot += $bobot;
                    $totalNilai += $nilai;
                    $jumlahTugas++;
                }
            }

            $nilaiAkhir = $jumlahTugas > 0 ? round($totalNilai / $jumlahTugas, 2) : 0;

            Progress::updateOrCreate(
                ['pegawai_id' => $pegawai->id],
                ['total_bobot' => $totalBobot, 'nilai_akhir' => $nilaiAkhir]
            );
        }

        // 🔹 Data Tabel Kinerja (paginate tugas langsung)
        $tugas = Tugas::with(['pegawai', 'realisasi', 'jenisPekerjaan'])
                    ->paginate(3, ['*'], 'tugas_page');

        // 🔹 Data Tabel Nilai Akhir (paginate progress)
        $progress = Progress::with('pegawai')
                    ->paginate(5, ['*'], 'progress_page');

        return view('superadmin.progress.index', compact('tugas', 'progress'));
    }

    public function show($id)
    {
        $pegawai = Pegawai::with(['tugas.realisasi', 'tugas.jenisPekerjaan', 'progress'])->findOrFail($id);
        return view('superadmin.progress.detail', compact('pegawai'));
    }
}
