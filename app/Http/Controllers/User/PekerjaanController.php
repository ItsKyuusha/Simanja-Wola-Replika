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

        // Filter
        if ($request->filled('search')) {
            $tugasQuery->where('nama_tugas', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('jenis_pekerjaan')) {
            $tugasQuery->whereHas('jenisPekerjaan', function ($q) use ($request) {
                $q->where('nama_pekerjaan', 'like', '%' . $request->jenis_pekerjaan . '%');
            });
        }

        if ($request->filled('deadline_start') && $request->filled('deadline_end')) {
            $tugasQuery->whereBetween('deadline', [$request->deadline_start, $request->deadline_end]);
        } elseif ($request->filled('deadline_start')) {
            $tugasQuery->where('deadline', '>=', $request->deadline_start);
        } elseif ($request->filled('deadline_end')) {
            $tugasQuery->where('deadline', '<=', $request->deadline_end);
        }

        $tugas = $tugasQuery->get();

        foreach ($tugas as $t) {
            $total = $t->semuaRealisasi->sum('realisasi');
            $t->total_realisasi = $total;
            $t->kuantitas = $t->target > 0 ? round(($total / $t->target) * 100, 2) : 0;
            $t->kualitas  = $t->kuantitas;

            // jika selesai sebelum deadline → kualitas full
            if ($total >= $t->target) {
                $t->kualitas = 100;

                // cek apakah telat
                $lastDate = $t->semuaRealisasi->max('tanggal_realisasi');
                if ($lastDate && Carbon::parse($lastDate)->gt(Carbon::parse($t->deadline))) {
                    $hariTelat = Carbon::parse($lastDate)->diffInDays(Carbon::parse($t->deadline));
                    $t->kualitas = max(0, $t->kualitas - ($hariTelat * 10));
                }
            }

            // cek deadline untuk status
            $t->is_late = Carbon::now()->gt(Carbon::parse($t->deadline)) && $total < $t->target;

            // rincian histori
            $akumulasi = 0;
            $t->rincian = $t->semuaRealisasi->sortBy('tanggal_realisasi')->map(function ($r) use ($t, &$akumulasi) {
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
        }

        return view('user.pekerjaan.index', compact('tugas'));
    }

    public function storeRealisasi(Request $request, $tugas_id)
    {
        $pegawaiId = auth()->user()->pegawai_id;

        $tugas = Tugas::where('id', $tugas_id)
            ->where('pegawai_id', $pegawaiId)
            ->firstOrFail();

        $tanggalAwal  = $tugas->created_at->toDateString();
        $tanggalAkhir = $tugas->deadline;

        $validated = $request->validate([
            'realisasi' => 'required|numeric|min:1',
            'tanggal_realisasi' => "required|date|after_or_equal:$tanggalAwal",
            'catatan' => 'nullable|string',
            'file_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Hitung total yang sudah ada
        $currentTotal = $tugas->semuaRealisasi()->sum('realisasi');
        $sisa = $tugas->target - $currentTotal;

        if ($validated['realisasi'] > $sisa) {
            return back()->withErrors(['realisasi' => "Maksimal realisasi yang bisa dimasukkan hanya $sisa"]);
        }

        if ($request->hasFile('file_bukti')) {
            $validated['file_bukti'] = $request->file('file_bukti')->store('bukti', 'public');
        }

        $newTotal = $currentTotal + $validated['realisasi'];
        $target   = $tugas->target;

        // Hitung kuantitas & kualitas
        $nilaiKuantitas = $target > 0 ? round(($newTotal / $target) * 100, 2) : 0;
        $nilaiKualitas  = $newTotal >= $target ? 100 : $nilaiKuantitas;

        // Penalti keterlambatan (10% per hari)
        if (Carbon::parse($validated['tanggal_realisasi'])->gt(Carbon::parse($tugas->deadline))) {
            $hariTelat = Carbon::parse($validated['tanggal_realisasi'])
                ->diffInDays(Carbon::parse($tugas->deadline));
            $nilaiKualitas = max(0, $nilaiKualitas - ($hariTelat * 10));
        }

        $validated['tugas_id'] = $tugas->id;
        $validated['nilai_kuantitas'] = $nilaiKuantitas;
        $validated['nilai_kualitas']  = $nilaiKualitas;

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
        $tanggalAwal  = $tugas->created_at->toDateString();
        $tanggalAkhir = $tugas->deadline;

        $validated = $request->validate([
            'realisasi' => 'required|numeric|min:1',
            'tanggal_realisasi' => "required|date|after_or_equal:$tanggalAwal",
            'catatan' => 'nullable|string',
            'file_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Hitung total selain yang sedang diedit
        $otherTotal = $tugas->semuaRealisasi()->where('id', '<>', $realisasi->id)->sum('realisasi');
        $sisa = $tugas->target - $otherTotal;

        if ($validated['realisasi'] > $sisa) {
            return back()->withErrors(['realisasi' => "Maksimal realisasi yang bisa dimasukkan hanya $sisa"]);
        }

        if ($request->hasFile('file_bukti')) {
            if ($realisasi->file_bukti) {
                Storage::disk('public')->delete($realisasi->file_bukti);
            }
            $validated['file_bukti'] = $request->file('file_bukti')->store('bukti', 'public');
        } else {
            $validated['file_bukti'] = $realisasi->file_bukti;
        }

        $newTotal = $otherTotal + $validated['realisasi'];
        $target   = $tugas->target;

        // Hitung kuantitas & kualitas
        $nilaiKuantitas = $target > 0 ? round(($newTotal / $target) * 100, 2) : 0;
        $nilaiKualitas  = $newTotal >= $target ? 100 : $nilaiKuantitas;

        // Penalti keterlambatan
        if (Carbon::parse($validated['tanggal_realisasi'])->gt(Carbon::parse($tugas->deadline))) {
            $hariTelat = Carbon::parse($validated['tanggal_realisasi'])
                ->diffInDays(Carbon::parse($tugas->deadline));
            $nilaiKualitas = max(0, $nilaiKualitas - ($hariTelat * 10));
        }

        $validated['nilai_kuantitas'] = $nilaiKuantitas;
        $validated['nilai_kualitas']  = $nilaiKualitas;

        $realisasi->update($validated);

        return back()->with('success', 'Realisasi berhasil diupdate.');
    }
}
