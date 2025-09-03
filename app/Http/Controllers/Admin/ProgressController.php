<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $teamId = auth()->user()->pegawai->team_id;
        $search = $request->input('search');

        $tugas = Tugas::with(['pegawai', 'realisasi'])
            ->whereHas('pegawai', function ($q) use ($teamId, $search) {
                $q->where('team_id', $teamId);

                if ($search) {
                    $q->where(function ($qq) use ($search) {
                        $qq->where('nama', 'like', "%{$search}%")
                            ->orWhere('nip', 'like', "%{$search}%");
                    });
                }
            })
            ->when($search, function ($q) use ($search) {
                $q->orWhere('nama_tugas', 'like', "%{$search}%");
            })
            ->get();

        return view('admin.progress.index', compact('tugas'));
    }

    /**
     * Export data Progress ke Excel
     */
    public function export(Request $request)
    {
        $teamId = auth()->user()->pegawai->team_id;
        $search = $request->input('search');

        $export = new class($teamId, $search) implements
            \Maatwebsite\Excel\Concerns\FromCollection,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\ShouldAutoSize,
            \Maatwebsite\Excel\Concerns\WithStyles
        {
            protected $teamId, $search;

            public function __construct($teamId, $search)
            {
                $this->teamId = $teamId;
                $this->search = $search;
            }

            public function collection()
            {
                $tugas = Tugas::with(['pegawai', 'realisasi'])
                    ->whereHas('pegawai', function ($q) {
                        $q->where('team_id', $this->teamId);

                        if ($this->search) {
                            $q->where(function ($qq) {
                                $qq->where('nama', 'like', "%{$this->search}%")
                                    ->orWhere('nip', 'like', "%{$this->search}%");
                            });
                        }
                    })
                    ->when($this->search, function ($q) {
                        $q->orWhere('nama_tugas', 'like', "%{$this->search}%");
                    })
                    ->get();

                return $tugas->values()->map(function ($tugas, $index) {
                    return [
                        'No'             => $index + 1,
                        'Nama Pegawai'   => $tugas->pegawai->nama ?? '',
                        'NIP'            => $tugas->pegawai->nip ?? '',
                        'Nama Tugas'     => $tugas->nama_tugas,
                        'Tanggal Mulai'  => $tugas->tanggal_mulai,
                        'Tanggal Selesai' => $tugas->tanggal_selesai,
                        'Status'         => $tugas->status ?? '-',
                        'Realisasi'      => optional($tugas->realisasi)->persentase . '%' ?? '0%',
                    ];
                });
            }

            public function headings(): array
            {
                return [
                    'No',
                    'Nama Pegawai',
                    'NIP',
                    'Nama Tugas',
                    'Tanggal Mulai',
                    'Tanggal Selesai',
                    'Status',
                    'Realisasi',
                ];
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

                // Kolom "No" rata tengah
                $sheet->getStyle('A2:A' . $highestRow)->applyFromArray([
                    'alignment' => ['horizontal' => 'center'],
                ]);

                return [];
            }
        };

        return Excel::download($export, 'progress.xlsx');
    }
}
