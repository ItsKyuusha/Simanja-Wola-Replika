<?php

namespace App\Exports;

use App\Models\JenisPekerjaan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JenisPekerjaanExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
                'Tim'                => $item->team->nama_tim ?? '-',
                'Tanggal Dibuat' => $item->created_at ? $item->created_at->format('Y-m-d') : '-',

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
            'Tim',
            'Tanggal Dibuat',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header (baris pertama)
            1 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => 'center'],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ]
                ]
            ],
        ];
    }
}
