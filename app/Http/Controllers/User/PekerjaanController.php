<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use App\Models\RealisasiTugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PekerjaanController extends Controller
{
    public function index(Request $request)
    {
        $pegawaiId = auth()->user()->pegawai_id;

        $tugasQuery = Tugas::with(['semuaRealisasi', 'jenisPekerjaan'])
            ->where('pegawai_id', $pegawaiId);

        // Filter nama pekerjaan
        if ($request->filled('search')) {
            $tugasQuery->whereHas('jenisPekerjaan', function ($q) use ($request) {
                $q->where('nama_pekerjaan', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('jenis_pekerjaan')) {
            $tugasQuery->whereHas('jenisPekerjaan', function ($q) use ($request) {
                $q->where('nama_pekerjaan', 'like', '%' . $request->jenis_pekerjaan . '%');
            });
        }

        // Filter deadline
        if ($request->filled('deadline_start') && $request->filled('deadline_end')) {
            $tugasQuery->whereBetween('deadline', [$request->deadline_start, $request->deadline_end]);
        } elseif ($request->filled('deadline_start')) {
            $tugasQuery->where('deadline', '>=', $request->deadline_start);
        } elseif ($request->filled('deadline_end')) {
            $tugasQuery->where('deadline', '<=', $request->deadline_end);
        }

        $tugas = $tugasQuery->get();

        foreach ($tugas as $t) {
            // total realisasi & progress
            $totalRealisasi = $t->semuaRealisasi->sum('realisasi');
            $progress = $t->target > 0 ? min($totalRealisasi / $t->target, 1) : 0;

            // bobot dari jenis pekerjaan
            $bobot = $t->jenisPekerjaan->bobot ?? 0;

            // keterlambatan (hari telat berdasarkan realisasi terakhir)
            $lastDate = $t->semuaRealisasi->max('tanggal_realisasi');
            $hariTelat = 0;
            if ($lastDate && Carbon::parse($lastDate)->gt(Carbon::parse($t->deadline))) {
                $hariTelat = Carbon::parse($lastDate)->diffInDays(Carbon::parse($t->deadline));
            }

            // penalti 10% per hari keterlambatan
            $penalti = $bobot * 0.1 * $hariTelat;

            // nilai akhir (bobot * progress – penalti)
            $nilaiAkhir = max(0, ($bobot * $progress) - $penalti);

            // atribut untuk Blade
            $t->setAttribute('bobot_asli', $bobot);
            $t->setAttribute('penalti', $penalti);
            $t->setAttribute('nilai_akhir', $nilaiAkhir);

            $t->setAttribute(
                'is_late',
                Carbon::now()->gt(Carbon::parse($t->deadline)) && $totalRealisasi < $t->target
            );

            // rincian histori realisasi
            $akumulasi = 0;
            $rincian = $t->semuaRealisasi->sortBy('tanggal_realisasi')->map(function ($r) use ($t, &$akumulasi) {
                $akumulasi += $r->realisasi;
                $persen = $t->target > 0 ? round(($akumulasi / $t->target) * 100, 2) : 0;

                return [
                    'id' => $r->id,
                    'tanggal_input' => $r->created_at->format('d-m-Y H:i'),
                    'tanggal_realisasi' => Carbon::parse($r->tanggal_realisasi)->format('d-m-Y'),
                    'jumlah' => $r->realisasi,
                    'catatan' => $r->catatan,
                    'akumulasi' => $akumulasi,
                    'persen' => $persen,
                    'file_bukti' => $r->file_bukti,
                ];
            });
            $t->setAttribute('rincian', $rincian);
        }

        return view('user.pekerjaan.index', compact('tugas'));
    }

    public function storeRealisasi(Request $request, $tugas_id)
    {
        $pegawaiId = auth()->user()->pegawai_id;

        $tugas = Tugas::where('id', $tugas_id)
            ->where('pegawai_id', $pegawaiId)
            ->firstOrFail();

        $tanggalAwal = $tugas->created_at->toDateString();

        $validated = $request->validate([
            'realisasi' => 'required|numeric|min:1',
            'tanggal_realisasi' => "required|date|after_or_equal:$tanggalAwal",
            'catatan' => 'nullable|string',
            'file_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // sisa target
        $currentTotal = $tugas->semuaRealisasi()->sum('realisasi');
        $sisa = $tugas->target - $currentTotal;
        if ($validated['realisasi'] > $sisa) {
            return back()->withErrors(['realisasi' => "Maksimal realisasi yang bisa dimasukkan hanya $sisa"]);
        }

        // upload file bukti
        if ($request->hasFile('file_bukti')) {
            $validated['file_bukti'] = $request->file('file_bukti')->store('bukti', 'public');
        }

        $validated['tugas_id'] = $tugas->id;

        RealisasiTugas::create($validated);

        return back()->with('success', 'Realisasi berhasil disimpan.');
    }

    public function updateRealisasi(Request $request, $id)
    {
        $pegawaiId = auth()->user()->pegawai_id;

        $realisasi = RealisasiTugas::where('id', $id)
            ->whereHas('tugas', fn($q) => $q->where('pegawai_id', $pegawaiId))
            ->firstOrFail();

        $tugas = $realisasi->tugas;
        $tanggalAwal = $tugas->created_at->toDateString();

        $validated = $request->validate([
            'realisasi' => 'required|numeric|min:1',
            'tanggal_realisasi' => "required|date|after_or_equal:$tanggalAwal",
            'catatan' => 'nullable|string',
            'file_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // sisa target (selain yang sedang diedit)
        $otherTotal = $tugas->semuaRealisasi()->where('id', '<>', $realisasi->id)->sum('realisasi');
        $sisa = $tugas->target - $otherTotal;
        if ($validated['realisasi'] > $sisa) {
            return back()->withErrors(['realisasi' => "Maksimal realisasi yang bisa dimasukkan hanya $sisa"]);
        }

        // update file bukti
        if ($request->hasFile('file_bukti')) {
            if ($realisasi->file_bukti) {
                Storage::disk('public')->delete($realisasi->file_bukti);
            }
            $validated['file_bukti'] = $request->file('file_bukti')->store('bukti', 'public');
        } else {
            $validated['file_bukti'] = $realisasi->file_bukti;
        }

        $realisasi->update($validated);

        return back()->with('success', 'Realisasi berhasil diupdate.');
    }
}
