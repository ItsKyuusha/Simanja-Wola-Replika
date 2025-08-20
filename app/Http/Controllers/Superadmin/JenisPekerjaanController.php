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
        $teams = Team::all(); // Untuk dropdown Tim

        // Query awal
        $query = JenisPekerjaan::with('team');

        // Filter pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pekerjaan', 'like', '%' . $search . '%')
                    ->orWhere('satuan', 'like', '%' . $search . '%')
                    ->orWhereHas('team', function ($query) use ($search) {
                        $query->where('nama_tim', 'like', '%' . $search . '%');
                    });
            });
        }

        $data = $query->get();

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

    /**
     * Export data Jenis Pekerjaan ke Excel
     */
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
                return JenisPekerjaan::with('team')->get()->map(function ($item) {
                    return [
                        'ID'                 => $item->id,
                        'Nama Pekerjaan'     => $item->nama_pekerjaan,
                        'Satuan'             => $item->satuan,
                        'Bobot'              => $item->bobot,
                        'Pemberi Pekerjaan'  => $item->pemberi_pekerjaan,
                        'Tim'                => $item->team->nama_tim ?? '-'
                    ];
                });
            }

            public function headings(): array
            {
                return [
                    'ID',
                    'Nama Pekerjaan',
                    'Satuan',
                    'Bobot',
                    'Pemberi Pekerjaan',
                    'Tim'
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => 'center'],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ]
                    ],
                ];
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
                // cek apakah kolom tim ada
                $team = null;
                if (!empty($row['tim'])) { // hasil heading 'Tim' dari export
                    $team = \App\Models\Team::where('nama_tim', trim($row['tim']))->first();
                } elseif (!empty($row['nama_tim'])) {
                    // kalau user bikin sendiri header 'nama_tim'
                    $team = \App\Models\Team::where('nama_tim', trim($row['nama_tim']))->first();
                }

                return new \App\Models\JenisPekerjaan([
                    'nama_pekerjaan'    => $row['nama_pekerjaan'] ?? null,
                    'satuan'            => $row['satuan'] ?? null,
                    'bobot'             => $row['bobot'] ?? null,
                    'pemberi_pekerjaan' => $row['pemberi_pekerjaan'] ?? null,
                    'tim_id'            => $team?->id,
                ]);
            }
        }, $request->file('file'));

        return back()->with('success', 'Data Jenis Pekerjaan berhasil diimport.');
    }
}
