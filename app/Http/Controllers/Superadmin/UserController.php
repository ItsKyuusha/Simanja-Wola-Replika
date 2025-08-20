<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Style\Border;


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

    /** ========================
     * EXPORT DATA
     * ======================== */
    public function export()
    {
        $export = new class implements
            \Maatwebsite\Excel\Concerns\FromCollection,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\ShouldAutoSize,
            \Maatwebsite\Excel\Concerns\WithStyles
        {
            public function collection()
            {
                $users = \App\Models\User::with('pegawai.team')
                    ->where('role', '!=', 'superadmin') // ✅ exclude superadmin
                    ->get();

                return $users->values()->map(function ($user, $index) {
                    return [
                        'No'           => $index + 1,
                        'Nama Pegawai' => $user->pegawai->nama ?? '',
                        'NIP'          => $user->pegawai->nip ?? '',
                        'Jabatan'      => $user->pegawai->jabatan ?? '',
                        'Team'         => $user->pegawai->team->nama_tim ?? '',
                        'Username'     => $user->name,
                        'Email'        => $user->email,
                        'Role'         => $user->role,
                    ];
                });
            }

            public function headings(): array
            {
                return [
                    'No',
                    'Nama Pegawai',
                    'NIP',
                    'Jabatan',
                    'Team',
                    'Username',
                    'Email',
                    'Role',
                ];
            }

            public function styles(Worksheet $sheet)
            {
                $highestRow    = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Style header
                $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ]
                    ]
                ]);

                // Style data (mulai dari baris ke-2)
                $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
                    'alignment' => ['horizontal' => 'left'],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ]
                    ]
                ]);

                // Kolom "No" rata tengah
                $sheet->getStyle('A2:A' . $highestRow)->applyFromArray([
                    'alignment' => ['horizontal' => 'center'],
                ]);

                return [];
            }
        };

        return Excel::download($export, 'users.xlsx');
    }
    /** ========================
     * IMPORT DATA
     * ======================== */
    public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls'
    ]);

    $import = new class implements ToModel, WithHeadingRow {
        public function model(array $row)
        {
            // Skip kalau email kosong
            if (empty($row['email'])) {
                return null;
            }

            // Cegah duplikasi user berdasarkan email
            if (User::where('email', $row['email'])->exists()) {
                return null;
            }

            // Cari / buat tim (kalau kosong jangan buat tim baru)
            $team = null;
            if (!empty($row['team'])) {
                $team = Team::firstOrCreate(['nama_tim' => trim($row['team'])]);
            }

            // Buat pegawai (skip kalau nama kosong)
            $pegawai = null;
            if (!empty($row['nama_pegawai'])) {
                $pegawai = Pegawai::create([
                    'nama'    => $row['nama_pegawai'],
                    'nip'     => $row['nip'] ?? null,
                    'jabatan' => $row['jabatan'] ?? null,
                    'team_id' => $team?->id,
                ]);
            }

            // Buat user
            return new User([
                'name'       => $row['username'] ?? ($pegawai->nama ?? 'user'),
                'email'      => $row['email'],
                'password'   => Hash::make('password123'), // default password
                'role'       => $row['role'] ?? 'user',
                'pegawai_id' => $pegawai?->id,
            ]);
        }
    };

    Excel::import($import, $request->file('file'));

    return back()->with('success', 'Data user berhasil diimport.');
}

}
