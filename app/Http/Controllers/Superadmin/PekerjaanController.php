<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Tugas;

class PekerjaanController extends Controller
{
    public function index()
    {
        $query = Tugas::query();

        // 🔎 Search nama_pekerjaan
        if ($search = request('search')) {
            $query->whereHas('jenisPekerjaan', function ($q) use ($search) {
                $q->where('nama_pekerjaan', 'like', '%' . $search . '%');
            });
        }

        // Filter deadline
        if ($deadlineMonth = request('deadline_month')) {
            $query->whereMonth('deadline', $deadlineMonth);
        }
        if ($deadlineYear = request('deadline_year')) {
            $query->whereYear('deadline', $deadlineYear);
        }

        // Filter realisasi yang approved
        if (request('realisasi_month') || request('realisasi_year')) {
            $query->whereHas('semuaRealisasi', function ($q) {
                $q->where('is_approved', true);
                if ($bulan = request('realisasi_month')) {
                    $q->whereMonth('tanggal_realisasi', $bulan);
                }
                if ($tahun = request('realisasi_year')) {
                    $q->whereYear('tanggal_realisasi', $tahun);
                }
            });
        }

        // Sorting
        if ($sortBy = request('sort_by')) {
            $sortOrder = request('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);
        }

        // 🔹 ambil semua untuk statistik
        $allTugas = $query->with([
            'jenisPekerjaan.team',
            // semua realisasi (approved + belum)
            'semuaRealisasi',
        ])->get();

        // 🔹 ambil untuk tabel (realisasi approved saja)
        $tugas = $query->with([
            'jenisPekerjaan.team',
            'semuaRealisasi' => function ($q) {
                $q->where('is_approved', true);
            }
        ])->paginate(10)->withQueryString();

        // 🔹 Hitung statistik
        $totalTugas   = $allTugas->count();
        $tugasSelesai = 0;
        $tugasOngoing = 0;
        $tugasBelum   = 0;

        foreach ($allTugas as $t) {
            // sum approved saja
            $totalApproved = $t->semuaRealisasi->where('is_approved', true)->sum('realisasi');
            // sum semua realisasi (approved + belum)
            $totalSemua = $t->semuaRealisasi->sum('realisasi');

            if ($t->target > 0 && $totalApproved >= $t->target) {
                // target sudah terpenuhi dan approved
                $tugasSelesai++;
            } elseif ($totalSemua > 0) {
                // sudah ada realisasi tapi belum approved penuh
                $tugasOngoing++;
            } else {
                $tugasBelum++;
            }
        }

        $persentaseSelesai = $totalTugas
            ? round(($tugasSelesai / $totalTugas) * 100, 2)
            : 0;

        return view('superadmin.pekerjaan.index', compact(
            'tugas',
            'totalTugas',
            'tugasSelesai',
            'tugasOngoing',
            'tugasBelum',
            'persentaseSelesai'
        ));
    }
}
