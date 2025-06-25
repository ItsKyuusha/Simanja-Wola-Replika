<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\JenisTim;
use Illuminate\Http\Request;

class MasterJenisTimController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        if ($search) {
            $tims = JenisTim::where('nama', 'like', "%{$search}%")->get();
        } else {
            $tims = JenisTim::all();
        }

        return view('superadmin.masterjenistim', compact('tims'));
    }

    public function create() { return view('superadmin.masterjenistim.create'); }

    public function store(Request $request)
    {   
        JenisTim::create($request->only('nama'));
        return redirect()->route('superadmin.masterjenistim')->with('success', 'Tim ditambahkan');
    }

    public function edit($id)
    {
        $tim = JenisTim::findOrFail($id);
        return view('superadmin.masterjenistim.edit', compact('tim'));
    }

    public function update(Request $request, $id)
    {
        JenisTim::findOrFail($id)->update($request->only('nama'));
        return redirect()->route('superadmin.masterjenistim')->with('success', 'Tim diperbarui');
    }

    public function destroy($id)
    {
        JenisTim::destroy($id);
        return back()->with('success', 'Tim dihapus');
    }
}

