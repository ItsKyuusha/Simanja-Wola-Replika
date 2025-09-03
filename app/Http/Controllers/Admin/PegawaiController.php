<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;

// Tambahan untuk export Excel
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $teamId = auth()->user()->pegawai->team_id;

        $searchTim = $request->input('search_tim');
        $searchGlobal = $request->input('search_global');

        // Pegawai dalam tim
        $pegawaiQuery = Pegawai::where('team_id', $teamId);
        if ($searchTim) {
            $pegawaiQuery->where(function ($q) use ($searchTim) {
                $q->where('nama', 'like', "%{$searchTim}%")
                    ->orWhere('nip', 'like', "%{$searchTim}%")
                    ->orWhere('jabatan', 'like', "%{$searchTim}%");
            });
        }
        $pegawai = $pegawaiQuery->get();

        // Pegawai global
        $pegawaiGlobalQuery = Pegawai::query();
        if ($searchGlobal) {
            $pegawaiGlobalQuery->where(function ($q) use ($searchGlobal) {
                $q->where('nama', 'like', "%{$searchGlobal}%")
                    ->orWhere('nip', 'like', "%{$searchGlobal}%")
                    ->orWhere('jabatan', 'like', "%{$searchGlobal}%");
            });
        }
        $pegawaiGlobal = $pegawaiGlobalQuery->get();

        return view('admin.pegawai.index', compact('pegawai', 'pegawaiGlobal', 'searchTim', 'searchGlobal'));
    }

    // ====== EXPORT EXCEL ======
    public function exportExcel(Request $request)
    {
        $teamId = auth()->user()->pegawai->team_id;

        // ========== EXPORT TIM ==========
        if ($request->has('tipe') && $request->tipe === 'tim') {
            return Excel::download(new class($teamId) implements FromCollection, WithHeadings, WithStyles {
                protected $teamId;
                public function __construct($teamId)
                {
                    $this->teamId = $teamId;
                }

                public function collection()
                {
                    return Pegawai::where('team_id', $this->teamId)
                        ->select('id', 'nama', 'nip', 'jabatan', 'team_id')
                        ->get();
                }

                public function headings(): array
                {
                    return ['ID', 'Nama', 'NIP', 'Jabatan', 'Team ID'];
                }

                public function styles(Worksheet $sheet)
                {
                    $highestRow    = $sheet->getHighestRow();
                    $highestColumn = $sheet->getHighestColumn();

                    // Header
                    $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => 'center'],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ]
                    ]);

                    // Data
                    $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
                        'alignment' => ['horizontal' => 'left'],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ]
                    ]);

                    // Kolom ID rata tengah
                    $sheet->getStyle('A2:A' . $highestRow)->applyFromArray([
                        'alignment' => ['horizontal' => 'center'],
                    ]);

                    return [];
                }
            }, 'pegawai_tim.xlsx');
        }

        // ========== EXPORT GLOBAL ==========
        return Excel::download(new class implements FromCollection, WithHeadings, WithStyles {
            public function collection()
            {
                return Pegawai::select('id', 'nama', 'nip', 'jabatan', 'team_id')->get();
            }

            public function headings(): array
            {
                return ['ID', 'Nama', 'NIP', 'Jabatan', 'Team ID'];
            }

            public function styles(Worksheet $sheet)
            {
                $highestRow    = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Header
                $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ]
                    ]
                ]);

                // Data
                $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
                    'alignment' => ['horizontal' => 'left'],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ]
                    ]
                ]);

                // Kolom ID rata tengah
                $sheet->getStyle('A2:A' . $highestRow)->applyFromArray([
                    'alignment' => ['horizontal' => 'center'],
                ]);

                return [];
            }
        }, 'pegawai_global.xlsx');
    }
}
