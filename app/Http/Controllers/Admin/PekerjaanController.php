<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\JenisPekerjaan;
use App\Models\Tugas;
use Illuminate\Http\Request;

class PekerjaanController extends Controller
{
    public function index(Request $request)
    {
        $teamId = auth()->user()->pegawai->team_id;
        $search = $request->input('search');

        $tugas = Tugas::with(['pegawai', 'jenisPekerjaan', 'realisasi'])
            ->whereHas('pegawai', function ($query) use ($teamId, $search) {
                $query->where('team_id', $teamId);

                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%");
                    });
                }
            })
            ->when($search, function ($query) use ($search) {
                $query->orWhere('nama_tugas', 'like', "%{$search}%");
            })
            ->get();

        $pegawai = Pegawai::where('team_id', $teamId)->get();
        $jenisPekerjaan = JenisPekerjaan::all();

        return view('admin.pekerjaan.index', compact('tugas', 'pegawai', 'jenisPekerjaan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tugas' => 'required',
            'pegawai_id' => 'required|exists:pegawais,id',
            'jenis_pekerjaan_id' => 'required|exists:jenis_pekerjaans,id',
            'target' => 'required|numeric',
            'satuan' => 'required|string',
            'deadline' => 'required|date',
        ]);

        $asalInstruksi = auth()->user()->pegawai->team->nama_tim ?? 'Tidak diketahui';

        Tugas::create([
            'nama_tugas'         => $request->nama_tugas,
            'pegawai_id'         => $request->pegawai_id,
            'jenis_pekerjaan_id' => $request->jenis_pekerjaan_id,
            'target'             => $request->target,
            'satuan'             => $request->satuan,
            'asal'               => $asalInstruksi,
            'deadline'           => $request->deadline,
        ]);

        return redirect()->route('admin.pekerjaan.index')->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_tugas' => 'required',
            'pegawai_id' => 'required|exists:pegawais,id',
            'jenis_pekerjaan_id' => 'required|exists:jenis_pekerjaans,id',
            'target' => 'required|numeric',
            'satuan' => 'required|string',
            'deadline' => 'required|date',
        ]);

        $tugas = Tugas::findOrFail($id);
        $asalInstruksi = auth()->user()->pegawai->team->nama_tim ?? 'Tidak diketahui';

        $tugas->update([
            'nama_tugas'         => $request->nama_tugas,
            'pegawai_id'         => $request->pegawai_id,
            'jenis_pekerjaan_id' => $request->jenis_pekerjaan_id,
            'target'             => $request->target,
            'satuan'             => $request->satuan,
            'asal'               => $asalInstruksi,
            'deadline'           => $request->deadline,
        ]);

        return redirect()->route('admin.pekerjaan.index')->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tugas = Tugas::findOrFail($id);
        $tugas->delete();

        return redirect()->route('admin.pekerjaan.index')->with('success', 'Tugas berhasil dihapus.');
    }
}
