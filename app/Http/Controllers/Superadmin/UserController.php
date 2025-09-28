<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    // =========================
    // INDEX
    // =========================
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::with(['pegawai.teams'])
            ->whereNotNull('pegawai_id')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%")
                        ->orWhereHas('pegawai', function ($q2) use ($search) {
                            $q2->where('nama', 'like', "%{$search}%")
                                ->orWhere('nip', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('name')
            ->paginate(20);

        $teams = Team::all();

        return view('superadmin.master_user.index', compact('users', 'teams'));
    }

    // =========================
    // STORE
    // =========================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'     => 'required',
            'nip'      => 'required|unique:pegawais,nip',
            'jabatan'  => 'required',
            'teams'   => 'required|array|min:1',
            'teams.*' => 'exists:teams,id',
            'leader'   => 'nullable|exists:teams,id', // ✅ cukup satu ID, bukan array
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:superadmin,admin,user',
        ]);


        // Buat pegawai
        $pegawai = Pegawai::create([
            'nama'    => $validated['nama'],
            'nip'     => $validated['nip'],
            'jabatan' => $validated['jabatan'],
        ]);

        // Sinkronisasi tim dan leader
        $syncData = [];
        foreach ($validated['teams'] as $teamId) {
            $isLeader = false;
            if (!empty($validated['leader']) && in_array($teamId, $validated['leader'])) {
                $existingLeader = Team::find($teamId)->pegawais()->wherePivot('is_leader', true)->first();
                if (!$existingLeader) $isLeader = true;
            }
            $syncData[$teamId] = ['is_leader' => $isLeader];
        }
        $pegawai->teams()->sync($syncData);

        // Buat user
        User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'role'       => $validated['role'],
            'pegawai_id' => $pegawai->id,
        ]);

        return back()->with('success', 'User & Pegawai berhasil ditambahkan.');
    }

    // =========================
    // UPDATE
    // =========================
    public function update(Request $request, $id)
    {
        $user = User::with('pegawai.teams')->findOrFail($id);

        $validated = $request->validate([
            'nama'    => 'required',
            'nip'     => 'required|unique:pegawais,nip,' . $user->pegawai_id,
            'jabatan' => 'required',
            'teams'   => 'required|array|min:1',
            'teams.*' => 'exists:teams,id',
            'leader'  => 'nullable|array',
            'leader.*' => 'exists:teams,id',
            'name'    => 'required',
            'email'   => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'role'    => 'required|in:superadmin,admin,user',
        ]);

        // Update pegawai
        $user->pegawai->update([
            'nama'    => $validated['nama'],
            'nip'     => $validated['nip'],
            'jabatan' => $validated['jabatan'],
        ]);

        // Sinkronisasi tim + leader
        $syncData = [];
        foreach ($validated['teams'] as $teamId) {
            $isLeader = false;
            if (!empty($validated['leader']) && in_array($teamId, $validated['leader'])) {
                $existingLeader = Team::find($teamId)
                    ->pegawais()
                    ->wherePivot('is_leader', true)
                    ->where('pegawai_id', '!=', $user->pegawai_id)
                    ->first();
                if (!$existingLeader) $isLeader = true;
            }
            $syncData[$teamId] = ['is_leader' => $isLeader];
        }
        $user->pegawai->teams()->sync($syncData);

        // Update user
        $user->update([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role'     => $validated['role'],
            'password' => !empty($validated['password']) ? Hash::make($validated['password']) : $user->password,
        ]);

        return back()->with('success', 'User & Pegawai berhasil diperbarui.');
    }

    // =========================
    // DELETE
    // =========================
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->pegawai) {
            $user->pegawai->teams()->detach();
            $user->pegawai->delete();
        }
        $user->delete();
        return back()->with('success', 'User & Pegawai berhasil dihapus.');
    }


    // =========================
// EXPORT
// =========================
public function export()
{
    $users = User::with(['pegawai.teams'])->get();

    return Excel::download(new class($users) implements \Maatwebsite\Excel\Concerns\FromCollection, 
                                                       \Maatwebsite\Excel\Concerns\WithHeadings, 
                                                       \Maatwebsite\Excel\Concerns\WithMapping {
        private $users;

        public function __construct($users)
        {
            $this->users = $users;
        }

        // Data yang diexport
        public function collection()
        {
            return $this->users;
        }

        // Header kolom
        public function headings(): array
        {
            return [
                'Nama Pegawai',
                'NIP',
                'Jabatan',
                'Email',
                'Role',
                'Tim',
                'Tim yang Dipimpin'
            ];
        }

        // Mapping data per baris
        public function map($user): array
        {
            return [
                $user->pegawai->nama ?? '-',
                $user->pegawai->nip ?? '-',
                $user->pegawai->jabatan ?? '-',
                $user->email,
                $user->role,
                $user->pegawai ? $user->pegawai->teams->pluck('nama_tim')->implode(', ') : '-',
                $user->pegawai ? $user->pegawai->teams->where('pivot.is_leader', true)->pluck('nama_tim')->implode(', ') : '-',
            ];
        }
    }, 'users.xlsx');
}

    // =========================
    // IMPORT
    // =========================
    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);

        $errors = []; // Menyimpan error per baris

        $import = new class($errors) implements \Maatwebsite\Excel\Concerns\ToCollection, \Maatwebsite\Excel\Concerns\WithHeadingRow {
            private $errors;

            public function __construct(&$errors)
            {
                $this->errors = &$errors;
            }

            public function collection(\Illuminate\Support\Collection $rows)
            {
                foreach ($rows as $index => $row) {
                    try {
                        if (empty($row['email'])) {
                            throw new \Exception('Email kosong');
                        }
                        if (\App\Models\User::where('email', $row['email'])->exists()) {
                            throw new \Exception('Email sudah ada');
                        }

                        // Buat pegawai
                        $pegawai = \App\Models\Pegawai::create([
                            'nama'    => $row['nama_pegawai'] ?? null,
                            'nip'     => $row['nip'] ?? null,
                            'jabatan' => $row['jabatan'] ?? null,
                        ]);

                        // Tangani tim
                        $teamIds = [];
                        $leaderIds = [];
                        if (!empty($row['teams'])) {
                            $teamNames = array_map('trim', explode(',', $row['teams']));
                            $teamIds = \App\Models\Team::whereIn('nama_tim', $teamNames)->pluck('id')->toArray();
                        }
                        if (!empty($row['leaders'])) {
                            $leaderNames = array_map('trim', explode(',', $row['leaders']));
                            $leaderIds = \App\Models\Team::whereIn('nama_tim', $leaderNames)->pluck('id')->toArray();
                        }

                        // Sinkronisasi tim + leader
                        $syncData = collect($teamIds)->mapWithKeys(function ($teamId) use ($leaderIds) {
                            $existingLeader = \App\Models\Team::find($teamId)
                                ->pegawais()
                                ->wherePivot('is_leader', true)
                                ->first();
                            return [
                                $teamId => ['is_leader' => in_array($teamId, $leaderIds) && !$existingLeader]
                            ];
                        })->toArray();
                        $pegawai->teams()->sync($syncData);

                        // Buat user
                        \App\Models\User::create([
                            'name'       => $row['username'] ?? ($pegawai->nama ?? 'user'),
                            'email'      => $row['email'],
                            'password'   => Hash::make($row['password'] ?? 'password123'),
                            'role'       => $row['role'] ?? 'user',
                            'pegawai_id' => $pegawai->id,
                        ]);
                    } catch (\Exception $e) {
                        $this->errors[] = "Baris " . ($index + 2) . " gagal: " . $e->getMessage();
                    }
                }
            }
        };

        Excel::import($import, $request->file('file'));

        if (!empty($errors)) {
            $errorMessage = implode('<br>', $errors);
            return back()->with('error', "Beberapa data gagal diimport:<br>" . $errorMessage);
        }

        return back()->with('success', 'Data user berhasil diimport.');
    }
}
