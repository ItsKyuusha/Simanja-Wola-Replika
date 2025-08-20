<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use App\Models\Progress;
use Carbon\Carbon;

class PekerjaanController extends Controller
{
    public function index()
    {
        $query = Tugas::query();

        // Searching
        if ($search = request('search')) {
            $query->where('nama_tugas', 'like', '%' . $search . '%');
        }

        // Filtering by deadline month and year
        if ($deadlineMonth = request('deadline_month')) {
            $query->whereMonth('deadline', $deadlineMonth);
        }

        if ($deadlineYear = request('deadline_year')) {
            $query->whereYear('deadline', $deadlineYear);
        }

        // Filtering by realisasi month and year using whereHas
        if (request('realisasi_month') || request('realisasi_year')) {
            $query->whereHas('realisasi', function ($q) {
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

        // Ambil semua data untuk perhitungan
$allTugas = $query->with(['jenisPekerjaan', 'realisasi'])->get();

// Data untuk tabel (pagination)
$tugas = $query->with(['jenisPekerjaan', 'realisasi'])->paginate(10)->withQueryString();

// Perhitungan progress pakai semua data
$totalTugas = $allTugas->count();
$tugasSelesai = $allTugas->where('realisasi.realisasi', '>=', 100)->count();
$tugasOngoing = $allTugas->where('realisasi.realisasi', '>', 0)->where('realisasi.realisasi', '<', 100)->count();
$tugasBelum = $totalTugas - $tugasSelesai - $tugasOngoing;
$persentaseSelesai = $totalTugas ? round(($tugasSelesai / $totalTugas) * 100, 2) : 0;

return view('superadmin.pekerjaan.index', compact(
    'tugas',
    'totalTugas',
    'tugasSelesai',
    'tugasOngoing',
    'tugasBelum',
    'persentaseSelesai'
));

}}
