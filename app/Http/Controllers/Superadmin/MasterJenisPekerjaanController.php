<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\JenisPekerjaan;
use Illuminate\Http\Request;

class MasterJenisPekerjaanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        if ($search) {
            $data = JenisPekerjaan::where('nama', 'like', "%{$search}%")
                ->orWhere('satuan', 'like', "%{$search}%")
                ->orWhere('pemberi_pekerjaan', 'like', "%{$search}%")
                ->get();
        } else {
            $data = JenisPekerjaan::all();
        }

        return view('superadmin.masterjenispekerjaan', compact('data'));
    }

    public function create() { return view('superadmin.masterjenispekerjaan.create'); }

    public function store(Request $request)
    {
        JenisPekerjaan::create($request->all());
        return redirect()->route('superadmin.masterjenispekerjaan')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = JenisPekerjaan::findOrFail($id);
        return view('superadmin.masterjenispekerjaan.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        JenisPekerjaan::findOrFail($id)->update($request->all());
        return redirect()->route('superadmin.masterjenispekerjaan')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        JenisPekerjaan::destroy($id);
        return back()->with('success', 'Data dihapus');
    }
}

