<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;

class JenisTimController extends Controller
{
    public function index()
    {
        $data = Team::all();
        return view('superadmin.jenis_tim.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_tim' => 'required']);
        Team::create($request->all());
        return back()->with('success', 'Tim berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama_tim' => 'required']);
        $team = Team::findOrFail($id);
        $team->update($request->only('nama_tim'));

        return back()->with('success', 'Tim berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Team::findOrFail($id)->delete();
        return back()->with('success', 'tim berhasil dihapus.');
    }
}

