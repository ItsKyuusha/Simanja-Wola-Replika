<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\JenisPekerjaan;
use App\Models\Team;
use Illuminate\Http\Request;

class JenisPekerjaanController extends Controller
{
    public function index()
    {
        $data = JenisPekerjaan::with('team')->get(); // relasi team
        $teams = Team::all(); // untuk dropdown
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
