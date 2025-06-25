<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\JenisTim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MasterUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        if ($search) {
            $users = User::where('nama', 'like', "%{$search}%")
                         ->orWhere('nip', 'like', "%{$search}%")
                         ->orWhere('jabatan', 'like', "%{$search}%")
                         ->with('tim')
                         ->get();
        } else {
            $users = User::with('tim')->get();
        }

        $tims = JenisTim::all(); // Ambil data tim
        return view('superadmin.masteruser', compact('users', 'tims'));
    }

    public function create()
    {
        $tims = JenisTim::all();
        return view('superadmin.masteruser.create', compact('tims'));
    }

    public function store(Request $request)
    {
        User::create([
            'nama' => $request->nama,
            'nip' => $request->nip,
            'tim_id' => $request->tim_id,
            'jabatan' => $request->jabatan,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
        return redirect()->route('superadmin.masteruser')->with('success', 'User ditambahkan');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $tims = JenisTim::all();
        return view('superadmin.masteruser.edit', compact('user', 'tims'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'nama' => $request->nama,
            'nip' => $request->nip,
            'tim_id' => $request->tim_id,
            'jabatan' => $request->jabatan,
            'email' => $request->email,
            'role' => $request->role,
        ]);
        return redirect()->route('superadmin.masteruser')->with('success', 'User diperbarui');
    }

    public function destroy($id)
    {
        User::destroy($id);
        return back()->with('success', 'User dihapus');
    }
}


