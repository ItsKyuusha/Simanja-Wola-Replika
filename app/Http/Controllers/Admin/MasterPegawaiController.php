<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request; // Ini yang benar
use App\Http\Controllers\Controller;
use App\Models\User;

class MasterPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $pegawai = User::where('nama', 'like', "%{$search}%")
                            ->orWhere('nip', 'like', "%{$search}%")
                            ->orWhere('jabatan', 'like', "%{$search}%")
                            ->with('tim') // Pastikan relasi tim di-load
                            ->get();
        } else {
            $pegawai = User::with('tim')->get();
        }

        return view('admin.masterpegawai', compact('pegawai'));
    }
}


