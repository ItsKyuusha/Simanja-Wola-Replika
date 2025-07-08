<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use App\Models\RealisasiTugas;
use Illuminate\Http\Request;

class PekerjaanController extends Controller
{
    public function index()
    {
        $pegawaiId = auth()->user()->pegawai_id;

        $tugas = Tugas::with('realisasi', 'jenisPekerjaan')
            ->where('pegawai_id', $pegawaiId)
            ->get();

        return view('user.pekerjaan.index', compact('tugas'));
    }

    public function storeRealisasi(Request $request, $tugas_id)
    {
        $pegawaiId = auth()->user()->pegawai_id;

        $tugas = Tugas::where('id', $tugas_id)
            ->where('pegawai_id', $pegawaiId)
            ->firstOrFail();

        $validated = $request->validate([
            'realisasi' => 'required|numeric',
            'tanggal_realisasi' => 'required|date',
            'nilai_kualitas' => 'nullable|integer|min:0|max:100',
            'nilai_kuantitas' => 'nullable|integer|min:0|max:100',
            'catatan' => 'nullable|string',
            'file_bukti' => 'nullable|file|max:2048'
        ]);

        $validated['tugas_id'] = $tugas->id;

        if ($request->hasFile('file_bukti')) {
            $validated['file_bukti'] = $request->file('file_bukti')->store('bukti', 'public');
        }

        RealisasiTugas::create($validated);

        return back()->with('success', 'Realisasi berhasil disimpan.');
    }

    public function updateRealisasi(Request $request, $id)
    {
        $pegawaiId = auth()->user()->pegawai_id;

        $realisasi = RealisasiTugas::where('id', $id)
            ->whereHas('tugas', function ($query) use ($pegawaiId) {
                $query->where('pegawai_id', $pegawaiId);
            })
            ->firstOrFail();

        $validated = $request->validate([
            'realisasi' => 'required|numeric',
            'tanggal_realisasi' => 'required|date',
            'nilai_kualitas' => 'nullable|integer|min:0|max:100',
            'nilai_kuantitas' => 'nullable|integer|min:0|max:100',
            'catatan' => 'nullable|string',
            'file_bukti' => 'nullable|file|max:2048'
        ]);

        if ($request->hasFile('file_bukti')) {
            // Hapus file lama jika ada
            if ($realisasi->file_bukti) {
                \Storage::disk('public')->delete($realisasi->file_bukti);
            }
            $validated['file_bukti'] = $request->file('file_bukti')->store('bukti', 'public');
        }

        $realisasi->update($validated);

        return back()->with('success', 'Realisasi berhasil diupdate.');
    }

}
