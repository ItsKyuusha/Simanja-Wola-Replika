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

        // Filtering by realisasi month and year
        if ($realisasiMonth = request('realisasi_month')) {
            $query->whereMonth('realisasi.tanggal_realisasi', $realisasiMonth);
        }

        if ($realisasiYear = request('realisasi_year')) {
            $query->whereYear('realisasi.tanggal_realisasi', $realisasiYear);
        }

        // Sorting
        if ($sortBy = request('sort_by')) {
            $sortOrder = request('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);
        }

        // Execute the query
        $tugas = $query->with(['jenisPekerjaan', 'realisasi'])->get();

        // Calculate the summary data
        $totalTugas = $tugas->count();
        $tugasSelesai = $tugas->where('realisasi.realisasi', '>=', 100)->count();
        $tugasOngoing = $tugas->where('realisasi.realisasi', '>', 0)->where('realisasi.realisasi', '<', 100)->count();
        $tugasBelum = $totalTugas - $tugasSelesai - $tugasOngoing;
        $persentaseSelesai = $totalTugas ? round(($tugasSelesai / $totalTugas) * 100, 2) : 0;

        return view('superadmin.pekerjaan.index', compact('tugas', 'totalTugas', 'tugasSelesai', 'tugasOngoing', 'tugasBelum', 'persentaseSelesai'));
    }
}
