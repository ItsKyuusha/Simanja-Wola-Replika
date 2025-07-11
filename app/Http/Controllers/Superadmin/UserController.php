<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::with(['pegawai.team'])
            ->whereNotNull('pegawai_id')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhereHas('pegawai', function ($q2) use ($search) {
                        $q2->where('nama', 'like', '%' . $search . '%')
                            ->orWhere('nip', 'like', '%' . $search . '%');
                    });
                });
            })
            ->get();

        $teams = Team::all();

        return view('superadmin.master_user.index', compact('users', 'teams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'nip' => 'required|unique:pegawais,nip',
            'jabatan' => 'required',
            'team_id' => 'required|exists:teams,id',

            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:superadmin,admin,user',
        ]);

        // 1. Buat Pegawai dulu
        $pegawai = Pegawai::create([
            'nama' => $validated['nama'],
            'nip' => $validated['nip'],
            'jabatan' => $validated['jabatan'],
            'team_id' => $validated['team_id'],
        ]);

        // 2. Buat User, kaitkan ke pegawai
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'pegawai_id' => $pegawai->id,
        ]);

        return back()->with('success', 'User & Pegawai berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::with('pegawai')->findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required',
            'nip' => 'required|unique:pegawais,nip,' . $user->pegawai_id,
            'jabatan' => 'required',
            'team_id' => 'required|exists:teams,id',

            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'role' => 'required|in:superadmin,admin,user',
        ]);

        // Update pegawai
        $user->pegawai->update([
            'nama' => $validated['nama'],
            'nip' => $validated['nip'],
            'jabatan' => $validated['jabatan'],
            'team_id' => $validated['team_id'],
        ]);

        // Update user
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'User & Pegawai berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Optional: hapus juga pegawai
        if ($user->pegawai) {
            $user->pegawai->delete();
        }

        $user->delete();

        return back()->with('success', 'User & Pegawai berhasil dihapus.');
    }
}
