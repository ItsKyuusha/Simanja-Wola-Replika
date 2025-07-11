<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\JenisPekerjaan;
use App\Models\Team;
use Illuminate\Http\Request;

class JenisPekerjaanController extends Controller
{
    public function index(Request $request)
    {
        $teams = Team::all(); // Untuk dropdown Tim

        // Cek apakah ada parameter search, jika ada lakukan pencarian
        $query = JenisPekerjaan::with('team');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pekerjaan', 'like', '%' . $search . '%')
                  ->orWhere('satuan', 'like', '%' . $search . '%')
                  ->orWhereHas('team', function($query) use ($search) {
                      $query->where('nama_tim', 'like', '%' . $search . '%');
                  });
            });
        }

        // Ambil data yang sudah difilter berdasarkan pencarian
        $data = $query->get();

        return view('superadmin.master_jenis_pekerjaan.index', compact('data', 'teams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pekerjaan' => 'required',
            'satuan' => 'required',
            'bobot' => 'required|numeric',
            'tim_id' => 'required|exists:teams,id'
        ]);

        JenisPekerjaan::create($request->all());
        return back()->with('success', 'Jenis pekerjaan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = JenisPekerjaan::findOrFail($id);

        $request->validate([
            'nama_pekerjaan' => 'required',
            'satuan' => 'required',
            'bobot' => 'required|numeric',
            'tim_id' => 'required|exists:teams,id'
        ]);

        $item->update($request->all());

        return back()->with('success', 'Jenis pekerjaan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        JenisPekerjaan::findOrFail($id)->delete();
        return back()->with('success', 'Jenis pekerjaan berhasil dihapus.');
    }
}
