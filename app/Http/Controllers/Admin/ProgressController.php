<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use App\Models\RealisasiTugas;
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
                    // Hitung total realisasi yang sudah di-approve
                    $totalRealisasi = $tugas->realisasi->where('is_approved', true)->sum('realisasi');
                    $jumlahTarget   = $tugas->target ?? 1;
                    $persentase     = round(($totalRealisasi / $jumlahTarget) * 100, 2);

                    // Tentukan status
                    if ($totalRealisasi == 0) {
                        $status = 'Belum Dikerjakan';
                    } elseif ($totalRealisasi < $jumlahTarget) {
                        $status = 'Ongoing';
                    } else {
                        $status = 'Selesai Dikerjakan';
                    }

                    return [
                        'No'              => $index + 1,
                        'Nama Pegawai'    => $tugas->pegawai->nama ?? '',
                        'NIP'             => $tugas->pegawai->nip ?? '',
                        'Nama Tugas'      => $tugas->nama_tugas,
                        'Tanggal Mulai'   => $tugas->tanggal_mulai,
                        'Tanggal Selesai' => $tugas->tanggal_selesai,
                        'Status'          => $status,
                        'Realisasi'       => $persentase . '%',
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

    /**
     * Approve realisasi
     */
    public function approve($id)
    {
        $realisasi = \App\Models\RealisasiTugas::with('tugas.pegawai')->findOrFail($id);
        $tugas = $realisasi->tugas;

        $user = auth()->user();

        // Guard case: pastikan user punya pegawai
        if (!$user->pegawai) {
            abort(403, 'User tidak terhubung dengan data pegawai.');
        }

        // Cek apakah pegawai login adalah pemberi pekerjaan (asal)
        if ($tugas->asal !== $user->pegawai->nama) {
            abort(403, 'Anda bukan pemberi pekerjaan, tidak bisa approve tugas ini.');
        }

        // Update hanya jika belum di-approve
        if (!$realisasi->is_approved) {
            $realisasi->update(['is_approved' => true]);
        }

        return redirect()->route('admin.progress.index')
            ->with('success', 'Realisasi berhasil di-approve!');
    }
}
