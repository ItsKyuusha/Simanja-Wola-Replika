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
                $data = \App\Models\JenisPekerjaan::with('team')->get();

                return $data->values()->map(function ($item, $index) {
                    return [
                        'No'                 => $index + 1, // ✅ nomor urut manual
                        'Nama Pekerjaan'     => $item->nama_pekerjaan,
                        'Satuan'             => $item->satuan,
                        'Bobot'              => $item->bobot,
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
                // total baris (header + data)
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $cellRange = 'A1:' . $highestColumn . $highestRow;

                // style header
                $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ]
                    ]
                ]);

                // style data
                $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ]
                    ]
                ]);

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
                // skip jika nama_pekerjaan kosong
                if (empty($row['nama_pekerjaan']) && empty($row['nama pekerjaan'])) {
                    return null;
                }

                // cari tim berdasarkan kolom 'Tim' atau 'nama_tim'
                $team = null;
                if (!empty($row['tim'])) {
                    $team = \App\Models\Team::where('nama_tim', trim($row['tim']))->first();
                } elseif (!empty($row['nama_tim'])) {
                    $team = \App\Models\Team::where('nama_tim', trim($row['nama_tim']))->first();
                }

                return new \App\Models\JenisPekerjaan([
                    'nama_pekerjaan'    => $row['nama_pekerjaan'] ?? $row['nama pekerjaan'] ?? null,
                    'satuan'            => $row['satuan'] ?? null,
                    'bobot'             => $row['bobot'] ?? 0, // default 0 biar aman
                    'pemberi_pekerjaan' => $row['pemberi_pekerjaan'] ?? $row['pemberi pekerjaan'] ?? null,
                    'tim_id'            => $team?->id,
                ]);
            }
        }, $request->file('file'));

        return back()->with('success', 'Data Jenis Pekerjaan berhasil diimport.');
    }
}
