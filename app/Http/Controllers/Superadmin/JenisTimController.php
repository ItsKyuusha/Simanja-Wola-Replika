<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class JenisTimController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Team::query();

        if ($search) {
            $query->where('nama_tim', 'like', "%{$search}%");
        }

        $data = $query->get();

        return view('superadmin.jenis_tim.index', compact('data'));
    }
    
    public function store(Request $request)
    {
        $request->validate(['nama_tim' => 'required']);
        Team::create($request->all());
        return back()->with('success', 'Tim berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama_tim' => 'required']);
        $team = Team::findOrFail($id);
        $team->update($request->only('nama_tim'));

        return back()->with('success', 'Tim berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Team::findOrFail($id)->delete();
        return back()->with('success', 'tim berhasil dihapus.');
    }
    public function export()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles {
            public function collection()
            {
                $data = Team::all();

                return $data->values()->map(function ($team, $index) {
                    return [
                        'No'        => $index + 1,
                        'Nama Tim'  => $team->nama_tim,
                    ];
                });
            }

            public function headings(): array
            {
                return ['No', 'Nama Tim'];
            }

            public function styles(Worksheet $sheet)
            {
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // style header
                $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);

                // style data
                $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);

                return [];
            }
        }, 'teams.xlsx');
    }

    /**
     * Import Data Tim dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new class implements ToModel, WithHeadingRow {
            public function model(array $row)
            {
                if (empty($row['nama_tim'])) {
                    return null; // skip kalau kosong
                }

                return new Team([
                    'nama_tim' => $row['nama_tim'],
                ]);
            }
        }, $request->file('file'));

        return back()->with('success', 'Data Tim berhasil diimport.');
    }   
}


