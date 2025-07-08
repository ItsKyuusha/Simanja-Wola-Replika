<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\JenisPekerjaan;
use App\Models\Tugas;
use Illuminate\Http\Request;

class PekerjaanController extends Controller
{
    public function index()
    {
        $teamId = auth()->user()->pegawai->team_id;

        $tugas = Tugas::whereHas('pegawai', fn($q) => $q->where('team_id', $teamId))
                      ->with(['pegawai', 'jenisPekerjaan', 'realisasi'])
                      ->get();
                      
        $pegawai = Pegawai::where('team_id', $teamId)->get();
        $jenisPekerjaan = JenisPekerjaan::all();
        return view('admin.pekerjaan.index', compact('tugas', 'pegawai', 'jenisPekerjaan'));
    }

    public function create()
    {
        $teamId = auth()->user()->pegawai->team_id;

        $pegawai = Pegawai::where('team_id', $teamId)->get();
        $jenisPekerjaan = JenisPekerjaan::all();

        return view('admin.pekerjaan.create', compact('pegawai', 'jenisPekerjaan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tugas' => 'required',
            'pegawai_id' => 'required|exists:pegawais,id',
            'jenis_pekerjaan_id' => 'required|exists:jenis_pekerjaans,id',
            'target' => 'required|numeric',
            'asal' => 'nullable|string',
            'satuan' => 'required|string',
            'deadline' => 'required|date'
        ]);

        Tugas::create($request->all());

        return redirect()->route('admin.pekerjaan.index')->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_tugas' => 'required',
            'pegawai_id' => 'required|exists:pegawais,id',
            'jenis_pekerjaan_id' => 'required|exists:jenis_pekerjaans,id',
            'target' => 'required|numeric',
            'asal' => 'nullable|string',
            'satuan' => 'required|string',
            'deadline' => 'required|date'
        ]);

        $tugas = Tugas::findOrFail($id);
        $tugas->update($request->all());

        return redirect()->route('admin.pekerjaan.index')->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tugas = Tugas::findOrFail($id);
        $tugas->delete();

        return redirect()->route('admin.pekerjaan.index')->with('success', 'Tugas berhasil dihapus.');
    }

}
