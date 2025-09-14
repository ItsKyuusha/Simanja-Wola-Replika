<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Progress;
use App\Models\Tugas;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProgressController extends Controller
{
    public function index()
    {
        // 🔹 Hitung nilai akhir tiap pegawai berdasarkan realisasi APPROVED saja
        $pegawais = Pegawai::with(['tugas.realisasi', 'tugas.jenisPekerjaan'])->get();

        foreach ($pegawais as $pegawai) {
            $totalNilai  = 0;
            $jumlahTugas = 0;

            foreach ($pegawai->tugas as $tugas) {
                if ($tugas->realisasi && $tugas->realisasi->is_approved) {
                    // hitung nilai akhir tiap tugas
                    $target    = $tugas->target ?? 0;
                    $realisasi = $tugas->realisasi->realisasi ?? 0;
                    $progress  = $target > 0 ? min($realisasi / $target, 1) : 0;

                    $bobot     = $tugas->jenisPekerjaan->bobot ?? 0;

                    // penalti (opsional)
                    $deadline  = $tugas->deadline;
                    $tglReal   = $tugas->realisasi->tanggal_realisasi;
                    $hariTelat = 0;
                    if ($deadline && $tglReal && strtotime($tglReal) > strtotime($deadline)) {
                        $hariTelat = (new \Carbon\Carbon($deadline))->diffInDays(new \Carbon\Carbon($tglReal));
                    }
                    $penalti   = $bobot * 0.1 * $hariTelat;

                    $nilaiAkhirTugas = max(0, ($bobot * $progress) - $penalti);

                    $totalNilai  += $nilaiAkhirTugas;
                    $jumlahTugas++;
                }
            }

            $nilaiAkhir = $jumlahTugas > 0 ? round($totalNilai / $jumlahTugas, 2) : 0;

            Progress::updateOrCreate(
                ['pegawai_id' => $pegawai->id],
                ['nilai_akhir' => $nilaiAkhir]
            );
        }

        // 🔹 Hanya tampilkan tugas yang sudah di-approve
        $tugas = Tugas::with(['pegawai', 'realisasi', 'jenisPekerjaan.team'])
            ->whereHas('realisasi', function ($q) {
                $q->where('is_approved', true);
            })
            ->when(request('search_tugas'), function ($query, $search) {
                $query->whereHas('jenisPekerjaan', function ($q) use ($search) {
                    $q->where('nama_pekerjaan', 'like', "%{$search}%");
                })
                    ->orWhereHas('pegawai', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%")
                            ->orWhere('nip', 'like', "%{$search}%");
                    })
                    ->orWhereHas('jenisPekerjaan.team', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%");
                    });
            })
            ->paginate(3, ['*'], 'tugas_page');

        // 🔹 Progress
        $progress = Progress::with('pegawai')
            ->when(request('search_progress'), function ($query, $search) {
                $query->whereHas('pegawai', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%");
                });
            })
            ->paginate(5, ['*'], 'progress_page');

        return view('superadmin.progress.index', compact('tugas', 'progress'));
    }

    public function show($id)
    {
        $pegawai = Pegawai::with([
            'tugas.realisasi' => function ($q) {
                $q->where('is_approved', true);
            },
            'tugas.jenisPekerjaan.team',
            'progress'
        ])
            ->findOrFail($id);

        return view('superadmin.progress.detail', compact('pegawai'));
    }

    public function exportKinerja()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles {
            public function collection()
            {
                $tugas = Tugas::with(['pegawai', 'realisasi', 'jenisPekerjaan.team'])->get();

                return $tugas->map(function ($tugas, $index) {
                    // hitung nilai akhir per tugas
                    $target    = $tugas->target ?? 0;
                    $realisasi = ($tugas->realisasi && $tugas->realisasi->is_approved)
                        ? $tugas->realisasi->realisasi : 0;
                    $progress  = $target > 0 ? min($realisasi / $target, 1) : 0;
                    $bobot     = $tugas->jenisPekerjaan->bobot ?? 0;

                    $deadline  = $tugas->deadline;
                    $tglReal   = $tugas->realisasi->tanggal_realisasi ?? null;
                    $hariTelat = 0;
                    if ($deadline && $tglReal && strtotime($tglReal) > strtotime($deadline)) {
                        $hariTelat = (new \Carbon\Carbon($deadline))->diffInDays(new \Carbon\Carbon($tglReal));
                    }
                    $penalti = $bobot * 0.1 * $hariTelat;

                    $nilaiAkhirTugas = max(0, ($bobot * $progress) - $penalti);

                    return [
                        'No'                => $index + 1,
                        'Nama Pegawai'      => $tugas->pegawai->nama ?? '-',
                        'Nama Pekerjaan'    => $tugas->jenisPekerjaan->nama_pekerjaan ?? '-',
                        'Nama Tim'          => $tugas->jenisPekerjaan->team->nama ?? '-',
                        'Asal'              => $tugas->asal ?? '-',
                        'Target'            => $tugas->target ?? 0,
                        'Realisasi'         => $realisasi,
                        'Satuan'            => $tugas->satuan ?? '-',
                        'Deadline'          => $tugas->deadline
                            ? date('d-m-Y', strtotime($tugas->deadline)) : '-',
                        'Tanggal Realisasi' => ($tugas->realisasi && $tugas->realisasi->is_approved && $tugas->realisasi->tanggal_realisasi)
                            ? date('d-m-Y', strtotime($tugas->realisasi->tanggal_realisasi)) : '-',
                        'Bobot'             => $bobot,
                        'Nilai Akhir'       => round($nilaiAkhirTugas, 2),
                        'Catatan'           => $tugas->realisasi->catatan ?? '-',
                        'Bukti'             => $tugas->realisasi->file_bukti ?? '-',
                    ];
                });
            }

            public function headings(): array
            {
                return [
                    'No',
                    'Nama Pegawai',
                    'Nama Pekerjaan',
                    'Nama Tim',
                    'Asal',
                    'Target',
                    'Realisasi',
                    'Satuan',
                    'Deadline',
                    'Tgl Realisasi',
                    'Bobot',
                    'Nilai Akhir',
                    'Catatan',
                    'Bukti',
                ];
            }

            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
            {
                $highestRow    = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFEFEFEF'],
                    ]
                ]);
                $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
                    'alignment' => ['horizontal' => 'left'],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
                $sheet->getStyle('A2:A' . $highestRow)->applyFromArray([
                    'alignment' => ['horizontal' => 'center'],
                ]);
                foreach (range('A', $highestColumn) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                return [];
            }
        }, 'kinerja.xlsx');
    }

    public function exportNilaiAkhir()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles {
            public function collection()
            {
                $progress = Progress::with('pegawai')->get();

                return $progress->map(function ($item, $index) {
                    return [
                        'No.'          => $index + 1,
                        'Nama Pegawai' => $item->pegawai->nama ?? '-',
                        'NIP'          => $item->pegawai->nip ?? '-',
                        'Nilai Akhir'  => $item->nilai_akhir,
                    ];
                });
            }

            public function headings(): array
            {
                return ['No.', 'Nama Pegawai', 'NIP', 'Nilai Akhir'];
            }

            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
            {
                $highestRow    = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFEFEFEF'],
                    ]
                ]);
                $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
                    'alignment' => ['horizontal' => 'left'],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
                $sheet->getStyle('A2:A' . $highestRow)->applyFromArray([
                    'alignment' => ['horizontal' => 'center'],
                ]);
                foreach (range('A', $highestColumn) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                return [];
            }
        }, 'nilai-akhir.xlsx');
    }
}
