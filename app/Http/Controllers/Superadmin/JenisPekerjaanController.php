<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\JenisPekerjaan;
use App\Models\Team;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JenisPekerjaanController extends Controller
{
    public function index(Request $request)
    {
        $teams = Team::whereHas('pegawais', function ($q) {
            $q->where('pegawai_team.is_leader', 1);
        })->get();

        $query = JenisPekerjaan::with('team');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pekerjaan', 'like', "%$search%")
                    ->orWhere('satuan', 'like', "%$search%")
                    ->orWhereHas('team', function ($q2) use ($search) {
                        $q2->where('nama_tim', 'like', "%$search%");
                    });
            });
        }

        $data = $query->get();

        return view('superadmin.master_jenis_pekerjaan.index', compact('data', 'teams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pekerjaan'    => 'required|string',
            'satuan'            => 'required|string',
            'bobot'             => [
                'required',
                'numeric',
                'min:0',
                'max:100',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'tim_id'            => [
                'required',
                'exists:teams,id',
                function ($attribute, $value, $fail) {
                    $team = Team::find($value);
                    if (!$team || !$team->pegawais()->where('pegawai_team.is_leader', 1)->exists()) {
                        $fail('Tim yang dipilih harus memiliki leader.');
                    }
                }
            ],
            'pemberi_pekerjaan' => 'nullable|string',
        ]);

        $bobot = str_replace(',', '.', $request->bobot);

        JenisPekerjaan::create([
            'nama_pekerjaan'    => $request->nama_pekerjaan,
            'satuan'            => $request->satuan,
            'bobot'             => floatval($bobot),
            'tim_id'            => $request->tim_id,
            'pemberi_pekerjaan' => $request->pemberi_pekerjaan,
        ]);

        return back()->with('success', 'Jenis pekerjaan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = JenisPekerjaan::findOrFail($id);

        $request->validate([
            'nama_pekerjaan'    => 'required|string',
            'satuan'            => 'required|string',
            'bobot'             => [
                'required',
                'numeric',
                'min:0',
                'max:100',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'tim_id'            => 'required|exists:teams,id',
            'pemberi_pekerjaan' => 'nullable|string',
        ]);

        $bobot = str_replace(',', '.', $request->bobot);

        $item->update([
            'nama_pekerjaan'    => $request->nama_pekerjaan,
            'satuan'            => $request->satuan,
            'bobot'             => floatval($bobot),
            'tim_id'            => $request->tim_id,
            'pemberi_pekerjaan' => $request->pemberi_pekerjaan,
        ]);

        return back()->with('success', 'Jenis pekerjaan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        JenisPekerjaan::findOrFail($id)->delete();
        return back()->with('success', 'Jenis pekerjaan berhasil dihapus.');
    }

    public function export()
    {
        return Excel::download(new class implements
            \Maatwebsite\Excel\Concerns\FromCollection,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\ShouldAutoSize,
            \Maatwebsite\Excel\Concerns\WithStyles
        {
            public function collection()
            {
                $data = JenisPekerjaan::with('team')->get();

                return $data->values()->map(function ($item, $index) {
                    return [
                        'No'                 => $index + 1,
                        'Nama Pekerjaan'     => $item->nama_pekerjaan,
                        'Satuan'             => $item->satuan,
                        'Bobot'              => number_format($item->bobot, 2, ',', '.'),
                        'Pemberi Pekerjaan'  => $item->pemberi_pekerjaan,
                        'Tim'                => $item->team->nama_tim ?? '-',
                    ];
                });
            }

            public function headings(): array
            {
                return [
                    'No',
                    'Nama Pekerjaan',
                    'Satuan',
                    'Bobot',
                    'Pemberi Pekerjaan',
                    'Tim'
                ];
            }

            public function styles(Worksheet $sheet)
            {
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ]
                    ]
                ]);

                $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ]
                    ]
                ]);

                $sheet->getStyle('D2:D' . $highestRow)->getNumberFormat()->setFormatCode('0.00');

                return [];
            }
        }, 'jenis_pekerjaan.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new class implements ToModel, WithHeadingRow {
            public function model(array $row)
            {
                if (empty($row['nama_pekerjaan']) && empty($row['nama pekerjaan'])) {
                    return null;
                }

               $team = null;
$teamName = $row['tim'] ?? $row['nama_tim'] ?? null;

if ($teamName) {
    // rapikan: hapus spasi depan/belakang, ganti spasi ganda jadi satu
    $cleanName = preg_replace('/\s+/u', ' ', trim($teamName));

    // hapus karakter non-printable (kadang muncul dari Excel)
    $cleanName = preg_replace('/[[:^print:]]/u', '', $cleanName);

    // 🔍 log untuk debug
    \Log::info('Cek Team dari Excel', [
        'raw'   => $teamName,
        'clean' => $cleanName,
    ]);

    // cari di DB (case-insensitive)
    $team = Team::whereRaw('LOWER(nama_tim) = ?', [strtolower($cleanName)])->first();

    // kalau tetap tidak ketemu, log juga
    if (!$team) {
        \Log::warning('Team tidak ditemukan di DB', [
            'search' => strtolower($cleanName),
        ]);
    }
}

                // 🔧 Perbaikan parsing bobot
                $bobot = $row['bobot'] ?? 0;
                $bobot = str_replace(',', '.', (string)$bobot);
                if (!is_numeric($bobot)) {
                    $bobot = 0;
                }

                return new JenisPekerjaan([
                    'nama_pekerjaan'    => $row['nama_pekerjaan'] ?? $row['nama pekerjaan'] ?? null,
                    'satuan'            => $row['satuan'] ?? null,
                    'bobot'             => floatval($bobot),
                    'pemberi_pekerjaan' => $row['pemberi_pekerjaan'] ?? $row['pemberi pekerjaan'] ?? null,
                    'tim_id'            => $team?->id,
                ]);
            }

            public function rules(): array
            {
                return [
                    'bobot' => ['numeric', 'min:0', 'max:100']
                ];
            }

            
        }, $request->file('file'));

        return back()->with('success', 'Data Jenis Pekerjaan berhasil diimport.');
    }
}
